<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductVariation;
use App\Models\UserCartItem;
use App\Services\StockService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    private StockService $stock;

    public function __construct(StockService $stock)
    {
        parent::__construct();
        $this->stock = $stock;
    }

    public function add(Request $request)
    {
        $user = Auth::guard('api_web')->user();
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'product_variation_id' => 'nullable|exists:product_variations,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $product = Product::query()->where('is_active', true)->findOrFail($request->product_id);
            $variation = null;
            if ($request->filled('product_variation_id')) {
                $variation = ProductVariation::query()
                    ->where('product_id', $product->id)
                    ->where('is_active', true)
                    ->findOrFail($request->product_variation_id);
            }

            $qty = max(1, (int) $request->input('quantity', 1));

            $cartItem = UserCartItem::query()
                ->where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->where(function ($q) use ($variation) {
                    if ($variation) {
                        $q->where('product_variation_id', $variation->id);
                    } else {
                        $q->whereNull('product_variation_id');
                    }
                })
                ->lockForUpdate()
                ->first();

            $this->resolveAndValidateUnitPrice($product, $variation);
            $newQty = $cartItem ? ((int) $cartItem->quantity + $qty) : $qty;
            $this->assertStockAvailable($product, $variation, $newQty);

            if ($cartItem) {
                $cartItem->update(['quantity' => $newQty]);
            } else {
                $cartItem = UserCartItem::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'product_variation_id' => $variation?->id,
                    'quantity' => $newQty,
                ]);
            }

            DB::commit();
            return $this->responseSuccess($this->cartSummary($user->id), 'Item added to cart successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage());
        }
    }

    public function list()
    {
        try {
            $user = Auth::guard('api_web')->user();
            return $this->responseSuccess($this->cartSummary($user->id));
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function updateQty(Request $request)
    {
        $user = Auth::guard('api_web')->user();
        $validator = Validator::make($request->all(), [
            'cart_item_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $cartItem = UserCartItem::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->findOrFail($request->cart_item_id);

            $product = Product::query()
                ->where('is_active', true)
                ->findOrFail($cartItem->product_id);
            $variation = null;
            if ($cartItem->product_variation_id) {
                $variation = ProductVariation::query()
                    ->where('product_id', $product->id)
                    ->where('is_active', true)
                    ->findOrFail($cartItem->product_variation_id);
            }

            $this->resolveAndValidateUnitPrice($product, $variation);
            $this->assertStockAvailable($product, $variation, (int) $request->quantity);
            $cartItem->update(['quantity' => (int) $request->quantity]);

            DB::commit();
            return $this->responseSuccess($this->cartSummary($user->id), 'Cart quantity updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage());
        }
    }

    public function remove(Request $request)
    {
        $user = Auth::guard('api_web')->user();
        $validator = Validator::make($request->all(), [
            'cart_item_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            UserCartItem::query()
                ->where('user_id', $user->id)
                ->where('id', $request->cart_item_id)
                ->delete();

            return $this->responseSuccess($this->cartSummary($user->id), 'Item removed from cart successfully.');
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function clear()
    {
        try {
            $user = Auth::guard('api_web')->user();
            UserCartItem::query()->where('user_id', $user->id)->delete();
            return $this->responseSuccess($this->cartSummary($user->id), 'Cart cleared successfully.');
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    private function assertStockAvailable(Product $product, ?ProductVariation $variation, int $quantity): void
    {
        $variationId = $variation?->id;
        $stock = ProductStock::query()
            ->where('product_id', $product->id)
            ->where(function ($q) use ($variationId) {
                if ($variationId) {
                    $q->where('product_variation_id', $variationId);
                } else {
                    $q->whereNull('product_variation_id');
                }
            })
            ->first();

        if (!$stock) {
            $fallbackStock = $variation ? (int) ($variation->stock ?? 0) : (int) ($product->stock ?? 0);
            $stock = ProductStock::create([
                'product_id' => $product->id,
                'product_variation_id' => $variation?->id,
                'stock_on_hand' => $fallbackStock,
                'stock_reserved' => 0,
                'stock_available' => max(0, $fallbackStock),
            ]);
        }

        // Self-heal: if stock_available is 0 but stock_on_hand is positive,
        // stock_reserved may be stale from old orders that never properly released it.
        // Recompute from only genuinely active pending PayWay reservations.
        if ((int) $stock->stock_available <= 0 && (int) $stock->stock_on_hand > 0) {
            $trueReserved  = $this->stock->computeActiveReserved($product->id, $variationId);
            $trueAvailable = max(0, (int) $stock->stock_on_hand - $trueReserved);
            $stock->stock_reserved  = $trueReserved;
            $stock->stock_available = $trueAvailable;
            $stock->save();
        }

        if ((int) $stock->stock_available < $quantity) {
            throw new Exception('Insufficient stock for selected quantity.');
        }
    }

    private function resolveAndValidateUnitPrice(Product $product, ?ProductVariation $variation): float
    {
        $unitPrice = (float) ($variation?->price ?? $product->price ?? 0);

        if ($unitPrice <= 0) {
            throw new Exception('Invalid product price.');
        }

        return $unitPrice;
    }

    private function cartSummary(int $userId): array
    {
        $items = UserCartItem::query()
            ->with([
                'product:id,sku,name_en,name_kh,description_en,description_kh,price,stock,is_active',
                'product.images' => fn($q) => $q->select('id', 'foreign_id', 'foreign_model', 'image')->limit(1),
                'variation:id,product_id,sku,barcode,name,price,stock,image_url,is_active',
            ])
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->get();

        $mapped = $items->map(function (UserCartItem $item) {
            $unitPrice = $item->variation?->price ?? $item->product?->price ?? 0;
            $lineTotal = (float) $unitPrice * (int) $item->quantity;

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_variation_id' => $item->product_variation_id,
                'quantity' => (int) $item->quantity,
                'unit_price' => (float) $unitPrice,
                'line_total' => $lineTotal,
                'product' => $item->product ? [
                    'id' => $item->product->id,
                    'sku' => $item->product->sku,
                    'name_en' => $item->product->name_en,
                    'name_kh' => $item->product->name_kh,
                    'description_en' => $item->product->description_en,
                    'description_kh' => $item->product->description_kh,
                    'price' => (float) $item->product->price,
                    'stock' => (int) ($item->product->stock ?? 0),
                    'is_active' => (bool) $item->product->is_active,
                    'image' => $item->product->images->first()?->url,
                ] : null,
                'product_variation' => $item->variation ? [
                    'id' => $item->variation->id,
                    'product_id' => $item->variation->product_id,
                    'sku' => $item->variation->sku,
                    'barcode' => $item->variation->barcode,
                    'name' => $item->variation->name,
                    'price' => (float) $item->variation->price,
                    'stock' => (int) ($item->variation->stock ?? 0),
                    'image_url' => $item->variation->image_url,
                    'is_active' => (bool) $item->variation->is_active,
                    'title' => $item->variation->title,
                    'description' => $item->variation->description,
                    'status' => $item->variation->status,
                ] : null,
                'variation' => $item->variation ? [
                    'id' => $item->variation->id,
                    'product_id' => $item->variation->product_id,
                    'sku' => $item->variation->sku,
                    'barcode' => $item->variation->barcode,
                    'name' => $item->variation->name,
                    'price' => (float) $item->variation->price,
                    'stock' => (int) ($item->variation->stock ?? 0),
                    'image_url' => $item->variation->image_url,
                    'is_active' => (bool) $item->variation->is_active,
                    'title' => $item->variation->title,
                    'description' => $item->variation->description,
                    'status' => $item->variation->status,
                ] : null,
            ];
        })->values();

        return [
            'items' => $mapped,
            'item_count' => $mapped->count(),
            'total_quantity' => (int) $mapped->sum('quantity'),
            'sub_total' => (float) $mapped->sum('line_total'),
        ];
    }
}
