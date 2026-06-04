<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductStock;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ProductStockController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:product-stock-view', ['only' => ['index', 'data', 'history']]);
        $this->middleware('permission:product-stock-update', ['only' => ['adjust']]);
    }

    public function index()
    {
        return view('admin::pages.product-stock.index');
    }

    public function report()
    {
        return view('admin::pages.product-stock.report');
    }

    public function summary()
    {
        try {
            $stats = ProductStock::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN stock_available <= 0 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(CASE WHEN stock_available > 0 AND stock_available <= 5 THEN 1 ELSE 0 END) as low_stock,
                SUM(CASE WHEN stock_available > 5 THEN 1 ELSE 0 END) as in_stock
            ')->first();

            return response()->json($stats);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function data()
    {
        try {
            $pag = request('pag') ?? 50;
            $data = ProductStock::query()
                ->with(['product:id,sku,name_en,name_kh', 'variation:id,sku,name'])
                ->addSelect([
                    'latest_stock_history_at' => DB::table('stock_history as sh')
                        ->selectRaw('MAX(sh.created_at)')
                        ->whereColumn('sh.product_id', 'product_stocks.product_id')
                        ->where(function ($q) {
                            $q->whereColumn('sh.product_variation_id', 'product_stocks.product_variation_id')
                                ->orWhere(function ($sub) {
                                    $sub->whereNull('sh.product_variation_id')
                                        ->whereNull('product_stocks.product_variation_id');
                                });
                        }),
                ])
                ->when(request('search'), function ($q) {
                    $search = request('search');
                    $q->whereHas('product', function ($sub) use ($search) {
                        $sub->where('sku', 'LIKE', "%{$search}%")
                            ->orWhere('name_en', 'LIKE', "%{$search}%")
                            ->orWhere('name_kh', 'LIKE', "%{$search}%");
                    })->orWhereHas('variation', function ($sub) use ($search) {
                        $sub->where('sku', 'LIKE', "%{$search}%")
                            ->orWhere('name', 'LIKE', "%{$search}%");
                    });
                })
                ->orderByDesc('updated_at')
                ->paginate($pag);

            return $data;
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function history()
    {
        try {
            $pag = request('pag') ?? 50;

            if (filter_var(request('summary', false), FILTER_VALIDATE_BOOLEAN)) {
                $summary = DB::table('stock_history as sh')
                    ->leftJoin('products as p', 'p.id', '=', 'sh.product_id')
                    ->leftJoin('product_variations as pv', 'pv.id', '=', 'sh.product_variation_id')
                    ->selectRaw('
                        sh.product_id,
                        sh.product_variation_id,
                        p.sku as product_sku,
                        p.name_en as product_name_en,
                        pv.sku as variation_sku,
                        pv.name as variation_name,
                        MAX(sh.created_at) as latest_stock_history_at,
                        COUNT(*) as movement_count
                    ')
                    ->when(request('product_id'), fn($q) => $q->where('sh.product_id', request('product_id')))
                    ->when(request('transaction_type'), fn($q) => $q->where('sh.transaction_type', request('transaction_type')))
                    ->groupBy(
                        'sh.product_id',
                        'sh.product_variation_id',
                        'p.sku',
                        'p.name_en',
                        'pv.sku',
                        'pv.name',
                    )
                    ->orderByDesc('latest_stock_history_at')
                    ->paginate($pag);

                return $summary;
            }

            $query = DB::table('stock_history as sh')
                ->leftJoin('products as p', 'p.id', '=', 'sh.product_id')
                ->leftJoin('product_variations as pv', 'pv.id', '=', 'sh.product_variation_id')
                ->select(
                    'sh.id',
                    'sh.order_id',
                    'sh.product_id',
                    'sh.product_variation_id',
                    'sh.quantity',
                    'sh.transaction_type',
                    'sh.stock_before',
                    'sh.stock_after',
                    'sh.created_at',
                    'p.sku as product_sku',
                    'p.name_en as product_name_en',
                    'pv.sku as variation_sku',
                    'pv.name as variation_name',
                )
                ->when(request('product_id'), fn($q) => $q->where('sh.product_id', request('product_id')))
                ->when(request('transaction_type'), fn($q) => $q->where('sh.transaction_type', request('transaction_type')))
                ->orderByDesc('sh.id');

            return $query->paginate($pag);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function adjust(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:product_stocks,id',
                'adjust_qty' => 'required|integer|not_in:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => __('validate.attributes.required'),
                    'errors' => $validator->errors(),
                ], 422);
            }

            $stock = ProductStock::findOrFail($request->id);
            $before = (int) $stock->stock_on_hand;
            $adjust = (int) $request->adjust_qty;
            $after = $before + $adjust;

            if ($after < 0) {
                DB::rollBack();
                return $this->responseError('Stock on hand cannot be negative.');
            }

            $reserved = (int) $stock->stock_reserved;
            $available = max(0, $after - $reserved);

            $stock->update([
                'stock_on_hand' => $after,
                'stock_available' => $available,
            ]);

            if (Schema::hasTable('stock_history')) {
                $payload = [
                    'order_id' => 0,
                    'product_id' => $stock->product_id,
                    'product_variation_id' => $stock->product_variation_id,
                    'quantity' => abs($adjust),
                    'transaction_type' => 'adjustment',
                    'stock_before' => $before,
                    'stock_after' => $after,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                try {
                    DB::table('stock_history')->insert($payload);
                } catch (\Throwable $e) {
                    // Keep adjustment successful even if history insert is blocked by strict FK.
                }
            }

            DB::commit();
            return $this->responseSuccess(null, 'Stock adjusted successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage());
        }
    }

    public function reportData(Request $request)
    {
        try {
            $pag = (int) ($request->get('pag') ?? 50);
            $pag = $pag > 0 ? min($pag, 200) : 50;

            return $this->buildStockReportQuery($request)
                ->orderBy('product_name_en')
                ->orderBy('variation_name')
                ->paginate($pag);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function reportExport(Request $request)
    {
        try {
            $rows = $this->buildStockReportQuery($request)
                ->orderBy('product_name_en')
                ->orderBy('variation_name')
                ->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Stock Report');

            $headers = ['No', 'Product', 'Product SKU', 'Variation', 'Variation SKU', 'On Hand', 'Reserved', 'Available', 'Moves', 'Latest Movement'];
            $sheet->fromArray($headers, null, 'A1');

            $headerStyle = [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

            foreach ($rows as $i => $row) {
                $rowNum = $i + 2;
                $sheet->fromArray([
                    $i + 1,
                    $row->product_name_en ?? '',
                    $row->product_sku ?? '',
                    $row->variation_name ?? 'Main Product',
                    $row->variation_sku ?? '',
                    (int) ($row->stock_on_hand ?? 0),
                    (int) ($row->stock_reserved ?? 0),
                    (int) ($row->stock_available ?? 0),
                    (int) ($row->movement_count ?? 0),
                    $row->latest_stock_history_at
                        ? Carbon::parse($row->latest_stock_history_at)->format('d/m/Y H:i')
                        : '',
                ], null, "A{$rowNum}");

                $available = (int) ($row->stock_available ?? 0);
                $color = $available <= 0 ? 'FEE2E2' : ($available <= 5 ? 'FEF3C7' : 'D1FAE5');
                $sheet->getStyle("A{$rowNum}:J{$rowNum}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB($color);
            }

            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $sheet->getStyle('A1:J1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $fileName = 'stock-report-' . now()->format('Ymd_His') . '.xlsx';

            $writer = new XlsxWriter($spreadsheet);
            $tempFile = tempnam(sys_get_temp_dir(), 'stock_report_');
            $writer->save($tempFile);

            return response()->download($tempFile, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    private function buildStockReportQuery(Request $request)
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $threshold = (int) $request->get('threshold', 5);
        $threshold = $threshold >= 0 ? $threshold : 5;

        $historyQuery = DB::table('stock_history as sh')
            ->selectRaw('
                sh.product_id,
                sh.product_variation_id,
                MAX(sh.created_at) as latest_stock_history_at,
                COUNT(*) as movement_count
            ')
            ->when($fromDate, function ($q) use ($fromDate) {
                $q->where('sh.created_at', '>=', Carbon::parse($fromDate)->startOfDay());
            })
            ->when($toDate, function ($q) use ($toDate) {
                $q->where('sh.created_at', '<=', Carbon::parse($toDate)->endOfDay());
            })
            ->groupBy('sh.product_id', 'sh.product_variation_id');

        return DB::table('product_stocks as ps')
            ->leftJoin('products as p', 'p.id', '=', 'ps.product_id')
            ->leftJoin('product_variations as pv', 'pv.id', '=', 'ps.product_variation_id')
            ->leftJoinSub($historyQuery, 'hm', function ($join) {
                $join->on('hm.product_id', '=', 'ps.product_id')
                    ->where(function ($q) {
                        $q->whereColumn('hm.product_variation_id', 'ps.product_variation_id')
                            ->orWhere(function ($sub) {
                                $sub->whereNull('hm.product_variation_id')
                                    ->whereNull('ps.product_variation_id');
                            });
                    });
            })
            ->select([
                'ps.id',
                'ps.product_id',
                'ps.product_variation_id',
                'ps.stock_on_hand',
                'ps.stock_reserved',
                'ps.stock_available',
                'p.sku as product_sku',
                'p.name_en as product_name_en',
                'pv.sku as variation_sku',
                'pv.name as variation_name',
                DB::raw('COALESCE(hm.movement_count, 0) as movement_count'),
                'hm.latest_stock_history_at',
            ])
            ->when($request->get('search'), function ($q) use ($request) {
                $search = trim((string) $request->get('search'));
                $q->where(function ($sub) use ($search) {
                    $sub->where('p.sku', 'LIKE', "%{$search}%")
                        ->orWhere('p.name_en', 'LIKE', "%{$search}%")
                        ->orWhere('pv.sku', 'LIKE', "%{$search}%")
                        ->orWhere('pv.name', 'LIKE', "%{$search}%");
                });
            })
            ->when(filter_var($request->get('low_stock_only', false), FILTER_VALIDATE_BOOLEAN), function ($q) use ($threshold) {
                $q->where('ps.stock_available', '<=', $threshold);
            });
    }
}
