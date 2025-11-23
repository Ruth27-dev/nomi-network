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
}
