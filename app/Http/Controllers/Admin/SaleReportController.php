<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductStock;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SaleReportController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:order-view');
    }

    public function index()
    {
        return view('admin::pages.sale-report.index');
    }

    public function summary(Request $request)
    {
        try {
            $totals = DB::table('orders as o')
                ->when($request->get('from_date'), fn($q) => $q->where('o.created_at', '>=', Carbon::parse($request->get('from_date'))->startOfDay()))
                ->when($request->get('to_date'),   fn($q) => $q->where('o.created_at', '<=', Carbon::parse($request->get('to_date'))->endOfDay()))
                ->when($request->get('status'),    fn($q) => $q->where('o.status', $request->get('status')))
                ->selectRaw('
                    COALESCE(SUM(o.grand_total), 0)  as total_sales,
                    COALESCE(SUM(o.sub_total), 0)    as total_cost,
                    COUNT(DISTINCT o.id)             as total_orders
                ')
                ->first();

            $productsSold = DB::table('order_items as oi')
                ->join('orders as o', 'o.id', '=', 'oi.order_id')
                ->when($request->get('from_date'), fn($q) => $q->where('o.created_at', '>=', Carbon::parse($request->get('from_date'))->startOfDay()))
                ->when($request->get('to_date'),   fn($q) => $q->where('o.created_at', '<=', Carbon::parse($request->get('to_date'))->endOfDay()))
                ->when($request->get('status'),    fn($q) => $q->where('o.status', $request->get('status')))
                ->sum('oi.quantity');

            $stockOnHand = (int) ProductStock::sum('stock_on_hand');

            return response()->json([
                'total_sales'   => (float) $totals->total_sales,
                'total_cost'    => (float) $totals->total_cost,
                'total_orders'  => (int)   $totals->total_orders,
                'products_sold' => (int)   $productsSold,
                'stock_on_hand' => $stockOnHand,
            ]);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function data(Request $request)
    {
        try {
            $pag = (int) ($request->get('pag') ?? 50);
            $pag = max(1, min($pag, 200));

            return $this->buildQuery($request)
                ->orderByDesc('o.id')
                ->paginate($pag);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function export(Request $request)
    {
        try {
            $rows = $this->buildQuery($request)->orderByDesc('o.id')->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Sale Report');

            $headers = ['No', 'Order#', 'Product', 'Variation', 'SKU', 'Qty', 'Unit Price', 'Line Total', 'Order Date', 'Customer', 'Phone', 'Status', 'Payment'];
            $sheet->fromArray($headers, null, 'A1');

            $headerStyle = [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);

            foreach ($rows as $i => $row) {
                $rowNum = $i + 2;
                $sheet->fromArray([
                    $i + 1,
                    $row->order_no         ?? '',
                    $row->product_name     ?? '',
                    $row->variation_name   ?? 'Main Product',
                    $row->product_sku      ?? '',
                    (int)   $row->quantity,
                    '$' . number_format((float) $row->unit_price, 2),
                    '$' . number_format((float) $row->line_total, 2),
                    $row->order_date ? Carbon::parse($row->order_date)->format('d/m/Y') : '',
                    $row->recipient_name  ?? '',
                    $row->recipient_phone ?? '',
                    ucfirst($row->status  ?? ''),
                    ucfirst($row->payment_status ?? ''),
                ], null, "A{$rowNum}");

                $status = $row->status ?? '';
                $color = match ($status) {
                    'completed' => 'D1FAE5',
                    'shipping'  => 'FEF3C7',
                    'cancelled' => 'FEE2E2',
                    default     => 'F9FAFB',
                };
                $sheet->getStyle("A{$rowNum}:M{$rowNum}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB($color);
            }

            foreach (range('A', 'M') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $fileName = 'sale-report-' . now()->format('Ymd_His') . '.xlsx';
            $writer   = new XlsxWriter($spreadsheet);
            $tempFile = tempnam(sys_get_temp_dir(), 'sale_report_');
            $writer->save($tempFile);

            return response()->download($tempFile, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    private function buildQuery(Request $request)
    {
        return DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->select([
                'oi.id',
                'o.id as order_id',
                'o.order_no',
                'oi.product_name',
                'oi.product_sku',
                'oi.variation_name',
                'oi.quantity',
                'oi.unit_price',
                'oi.line_total',
                'o.grand_total',
                'o.created_at as order_date',
                'o.recipient_name',
                'o.recipient_phone',
                'o.status',
                'o.payment_status',
            ])
            ->when($request->get('search'), function ($q) use ($request) {
                $search = trim((string) $request->get('search'));
                $q->where(function ($sub) use ($search) {
                    $sub->where('o.order_no',       'LIKE', "%{$search}%")
                        ->orWhere('o.recipient_name', 'LIKE', "%{$search}%")
                        ->orWhere('oi.product_name',  'LIKE', "%{$search}%")
                        ->orWhere('oi.product_sku',   'LIKE', "%{$search}%");
                });
            })
            ->when($request->get('status'),    fn($q) => $q->where('o.status', $request->get('status')))
            ->when($request->get('from_date'), fn($q) => $q->where('o.created_at', '>=', Carbon::parse($request->get('from_date'))->startOfDay()))
            ->when($request->get('to_date'),   fn($q) => $q->where('o.created_at', '<=', Carbon::parse($request->get('to_date'))->endOfDay()));
    }
}
