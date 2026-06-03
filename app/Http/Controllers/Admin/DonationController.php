<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Exception;

class DonationController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:donation-view', ['only' => ['index', 'data', 'detail']]);
    }

    public function index()
    {
        return view('admin::pages.donation.index');
    }

    public function data()
    {
        try {
            $pag = request('pag') ?? 50;

            $query = Donation::query()
                ->with('user:id,name,phone')
                ->when(request('payment_status'), fn($q) => $q->where('payment_status', request('payment_status')))
                ->when(request('donation_type'),  fn($q) => $q->where('donation_type',  request('donation_type')))
                ->when(request('search'), function ($q) {
                    $search = request('search');
                    $q->where(function ($sub) use ($search) {
                        $sub->where('tran_id',   'LIKE', "%{$search}%")
                            ->orWhere('firstname', 'LIKE', "%{$search}%")
                            ->orWhere('lastname',  'LIKE', "%{$search}%")
                            ->orWhere('email',     'LIKE', "%{$search}%");
                    });
                })
                ->when(request('date_from'), fn($q) => $q->whereDate('created_at', '>=', request('date_from')))
                ->when(request('date_to'),   fn($q) => $q->whereDate('created_at', '<=', request('date_to')));

            $totalAmount = (clone $query)->where('payment_status', 'paid')->sum('amount');

            $data = $query->orderByDesc('id')->paginate($pag);

            return response()->json([
                ...$data->toArray(),
                'otherData' => [
                    'total_amount' => number_format((float) $totalAmount, 2, '.', ''),
                ],
            ]);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function detail()
    {
        try {
            $data = Donation::query()
                ->with('user:id,name,phone')
                ->findOrFail(request('id'));

            return $this->responseSuccess($data);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }
}
