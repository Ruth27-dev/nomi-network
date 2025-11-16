<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Bill;
use App\Models\Branch;
use App\Models\Category;
use App\Models\ExchangeRate;
use App\Models\IngredientStockBatch;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\ItemVariate;
use App\Models\ListOfValue;
use App\Models\Order;
use App\Models\ProductVariate;
use App\Models\Receipt;
use App\Models\Shop;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FetchDataController extends Controller
{
    public function fetchCategoryData()
    {
        try {
            $pag = request('pag') ?? 50;
            $data = Category::query()
                ->when(request('branch_id'), function ($q) {
                    $q->where('branch_id', request('branch_id'));
                })
                ->when(request('type') == 'customer', function ($q) {
                    $q->where('is_sellable', true);
                })
                ->when(request('type') == 'inventory', function ($q) {
                    $q->where('is_sellable', false);
                })
                ->where('status', $this->active)
                ->when(request('search'), function ($q) {
                    $q->where(function ($q) {
                        $q->where('code', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('title->en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('description->en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('description->km', 'LIKE', '%' . request('search') . '%');
                    });
                })
                ->limit($pag)
                ->get();
            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function fetchItemData()
    {
        try {
            $pag = request('pag') ?? 50;
            $data = Item::query()
                ->when(request('branch_id'), function ($q) {
                    $q->where('branch_id', request('branch_id'));
                })
                ->when(request('shop_id'), function ($q) {
                    $q->with(['itemVariates' => function ($item) {
                        $item->whereHas('product');
                    }, 'branch.shop']);
                    $q->whereHas('itemVariates', function ($iv) {
                        $iv->where('status', $this->active);
                        $iv->whereHas('product');
                    });
                    $q->when(request('shop_id') !== 'exclude', function ($q) {
                        $q->whereHas('branch', function ($branch) {
                            $branch->where('shop_id', request('shop_id'));
                        });
                    });
                })
                ->when(request('type'), function ($q) {
                    $q->where('type', request('type'));
                })
                ->when(request('is_sellable'), function ($q) {
                    $q->where('is_sellable', true);
                })
                ->when(request('type') == 'customer', function ($q) {
                    $q->where('is_sellable', true);
                })
                ->when(request('type') == 'inventory', function ($q) {
                    $q->where('is_consumable', true);
                })
                ->where('status', $this->active)
                ->when(request('search'), function ($q) {
                    $q->where(function ($q) {
                        $q->where('code', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('title->en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                        $q->when(request('shop_id'), function ($search) {
                            $search->orWhereHas('branch', function ($branch) {
                                $branch->where('title->en', 'LIKE', '%' . request('search') . '%');
                                $branch->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                                $branch->orWhereHas('shop', function ($shop) {
                                    $shop->where('title->en', 'LIKE', '%' . request('search') . '%');
                                    $shop->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                                });
                            });
                        });
                    });
                })
                ->limit($pag)
                ->get();
            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function fetchItemVariateData()
    {
        try {
            $pag = request('pag') ?? 50;
            $data = ItemVariate::query()
                ->with([
                    'item:id,code,title,branch_id,unit_id',
                    'item.unit:id,title'
                ])
                ->when(request('branch_id'), function ($q) {
                    $q->whereHas('item', function ($item) {
                        $item->where('branch_id', request('branch_id'));
                    });
                })
                ->where('status', $this->active)
                ->when(request('is_consumable') == 'true', function ($q) {
                    $q->whereHas('item', function ($q) {
                        $q->where('is_consumable', true);
                    });
                })
                ->when(request('stock_inventory') == 'true', function ($q) {
                    $q->whereHas('stockInventoryBatches');
                    $q->with('stockInventoryBatches:id,item_variate_id,quantity,original_quantity');
                    $q->withSum('stockInventoryBatches as in_stock', 'quantity');
                })
                ->when(request('search'), function ($q) {
                    $q->where(function ($q) {
                        $q->where('title->en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                    });
                })
                ->limit($pag)
                ->get();
            return $data;
        } catch (Exception $e) {
            dd($e);
            return $this->responseError();
        }
    }

    public function fetchLOVData()
    {
        try {
            $pag = request('pag') ?? 50;
            $data = ListOfValue::query()
                ->when(request('branch_id'), function ($q) {
                    $q->where('branch_id', request('branch_id'));
                })
                ->where('status', $this->active)
                ->when(request('type'), function ($q) {
                    $q->where('type', request('type'));
                })
                ->when(request('search'), function ($q) {
                    $q->where(function ($q) {
                        $q->where('title->en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('code', 'LIKE', '%' . request('search') . '%');
                    });
                })
                ->limit($pag)
                ->get();
            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function fetchLOVMaxSequence()
    {
        try {
            $data = ListOfValue::query()
                ->where('type', request('type'))
                ->max('sequence');
            return $data + 1; // Return the next sequence number
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function fetchProductData()
    {
        try {
            $page = request('page') ?? 50;
            $data = ProductVariate::query()
                ->with('itemVariate.item.unit', 'bonusItems.item.unit')
                ->whereHas('itemVariate', function ($q) {
                    $q->where('status', config('dummy.status.active.key'));
                    $q->whereHas('item', function ($item) {
                        $item->where('status', $this->active);
                        $item->when(request('branch_id'), function ($branch) {
                            $branch->where('branch_id', request('branch_id'));
                        });
                    });
                })
                ->where('is_available', true)
                ->when(request('search'), function ($q) {
                    $q->where(function ($q) {
                        $q->where('description->en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('description->km', 'LIKE', '%' . request('search') . '%');
                        $q->orWhereHas('itemVariate', function ($q) {
                            $q->where('title->en', 'LIKE', '%' . request('search') . '%');
                            $q->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                        });
                    });
                })
                ->limit($page)
                ->get();
            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function fetchUserData()
    {
        try {
            $page = request('page') ?? 50;
            $data = User::query()
                ->when(request('branch_id'), function ($q) {
                    $q->where('branch_id', request('branch_id'));
                })
                ->when(request('type'), function ($q) {
                    $q->where('type', request('type'));
                })
                ->when(request('role'), function ($q) {
                    $q->where('role', request('role'));
                })
                ->when(request('is_online'), function ($q) {
                    $q->where('is_online', (bool) request('is_online'));
                })
                ->where('status', $this->active)
                ->when(request('search'), function ($q) {
                    $q->where('name', 'LIKE', '%' . request('search') . '%');
                    $q->orWhere('email', 'LIKE', '%' . request('search') . '%');
                    $q->orWhere('phone', 'LIKE', '%' . request('search') . '%');
                })
                ->limit($page)
                ->get();
            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function fetchShopData()
    {
        try {
            $page = request('page') ?? 50;
            $data = Shop::query()
                ->where('status', $this->active)
                ->when(request('selected_shop_ids'), function ($q) {
                    $q->whereNotIn('id', request('selected_shop_ids'));
                })
                ->when(request('search'), function ($q) {
                    $q->where('title->en', 'LIKE', '%' . request('search') . '%');
                    $q->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                    $q->orWhere('email', 'LIKE', '%' . request('search') . '%');
                    $q->orWhere('phone', 'LIKE', '%' . request('search') . '%');
                })
                ->limit($page)
                ->get();
            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function fetchBranchData()
    {
        try {
            $user = Auth::user();  // ✅ get full user object
            $userId = $user?->id;
            $role = $user?->role;
            $page = request('page') ?? 50;
            $data = Branch::query()
                ->with('shop')
                ->where('status', $this->active)
                ->when($role !== 'super_admin', function ($q) use ($userId) {
                    $q->whereHas('userBranches', function ($sub) use ($userId) {
                        $sub->where('user_id', $userId);
                    });
                })
                ->when(request('search'), function ($q) {
                    $q->where('title->en', 'LIKE', '%' . request('search') . '%');
                    $q->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                    $q->orWhere('email', 'LIKE', '%' . request('search') . '%');
                    $q->orWhere('phone', 'LIKE', '%' . request('search') . '%');
                    $q->orWhereHas('shop', function ($shop) {
                        $shop->where('title->en', 'LIKE', '%' . request('search') . '%');
                        $shop->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                        $shop->orWhere('email', 'LIKE', '%' . request('search') . '%');
                        $shop->orWhere('phone', 'LIKE', '%' . request('search') . '%');
                    });
                })
                ->limit($page)
                ->get();
            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }
    public function fetchBranchDefaultData(Request $request)
    {
        try {
            $page = request('page') ?? 50;
            $branchIds = $request->input('branch_select_id', []);
            if (!empty($branchIds)) {
                $data = Branch::query()
                    ->with('shop')
                    ->where('status', $this->active)
                    ->whereIn('id', $branchIds)
                    ->when(request('search'), function ($q) {
                        $q->where('title->en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('email', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('phone', 'LIKE', '%' . request('search') . '%');
                        $q->orWhereHas('shop', function ($shop) {
                            $shop->where('title->en', 'LIKE', '%' . request('search') . '%');
                            $shop->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                            $shop->orWhere('email', 'LIKE', '%' . request('search') . '%');
                            $shop->orWhere('phone', 'LIKE', '%' . request('search') . '%');
                        });
                    })
                    ->limit($page)
                    ->get();
            } else {
                $data = collect();
            }

            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function fetchExchangeRate()
    {
        try {
            $data = ExchangeRate::first();
            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function fetchStockBatchInventory()
    {
        try {
            $page = request('page') ?? 50;
            $data = IngredientStockBatch::query()
                ->select('id', 'item_variate_id', 'original_quantity', 'unit_price', 'expiry_date', 'received_at')
                ->with([
                    'itemVariate:id,item_id,title',
                    'itemVariate.item:id,unit_id,code',
                    'itemVariate.item.unit:id,title',
                ])
                ->when(request('branch_id'), function ($q) {
                    $q->whereHas('itemVariate.item', function ($item) {
                        $item->where('branch_id', request('branch_id'));
                    });
                })
                ->when(request('search'), function ($q) {
                    $q->whereHas('itemVariate', function ($variate) {
                        $variate->where('title->en', 'LIKE', '%' . request('search') . '%');
                        $variate->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                    });
                })
                ->orderBy('item_variate_id')
                ->limit($page)
                ->get();
            return $data;
        } catch (Exception $e) {
            dd($e);
            return $this->responseError();
        }
    }

    public function fetchCustomerData()
    {
        try {
            $phone = trim((string) request('phone'));

            if ($phone === '') {
                return response()->json([
                    'error' => true,
                    'message' => 'Phone is required.',
                ], 422);
            }

            $order = Order::select('name', 'phone', 'address', 'location')
                ->where('phone', $phone)
                ->latest('id')
                ->first();
            $user = User::select('name', 'phone', 'address')
                ->where('phone', $phone)
                ->where('type', 'user')
                ->latest('id')
                ->first();

            $picked = $order ?? $user;

            if (!$picked) {
                return response()->json([
                    'error' => false,
                    'data' => null,
                ], 200);
            }

            $data = [
                'name'     => $picked->name ?? null,
                'phone'    => $picked->phone ?? null,
                'address'  => $picked->address ?? null,
                'location' => $picked->location ?? null,
            ];

            return response()->json([
                'error' => false,
                'data'  => $data,
            ], 200);
        } catch (\Throwable $e) {
            return $this->responseError();
        }
    }

    public function fetchBillData($id)
    {
        try {
            $data = Bill::query()->with('group', 'order.user:id,name')
                ->where('order_id', $id)
                ->orderByDesc('id')->get();

            foreach ($data as $value) {
                $value->company = ListOfValue::where('type', 'company')->first();
                $value->bank_account = BankAccount::where('branch_id', $value?->order?->branch?->id)->first();
            }
            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function fetchReceiptData($orderId)
    {
        try {
            $data = Receipt::query()->with([
                'order.branch',
                'order.user:id,name',
                'order.payment' => function ($q) {
                    $q->where('status', 'PAID')
                        ->with('paymentType');;
                },
            ])
                ->where('order_id', $orderId)->first();
            $data->company = ListOfValue::where('type', 'company')->first();
            $data->bank_account = BankAccount::where('branch_id', $data?->order?->branch?->id)->first();

            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function fetchInvoiceData($orderId)
    {
        try {
            $data = Invoice::query()->with('order', 'branch')
                ->where('order_id', $orderId)->first();
            $data->company = ListOfValue::where('type', 'company')->first();
            $data->bank_account = BankAccount::where('branch_id', $data?->order?->branch?->id)->first();

            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }
}
