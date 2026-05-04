<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ListOfValue;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductLocation;
use App\Models\ProductSource;
use App\Models\ProductVariation;
use Exception;

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
                        $q->where('title->en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('slug', 'LIKE', '%' . request('search') . '%');
                    });
                })
                ->limit($pag)
                ->get();
            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function fetchProductData()
    {
        try {
            $pag = request('pag') ?? 50;
            $data = Product::query()
                ->where('is_active', true)
                ->when(request('search'), function ($q) {
                    $q->where(function ($q) {
                        $q->where('sku', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('name_en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('name_kh', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('description_en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('description_kh', 'LIKE', '%' . request('search') . '%');
                    });
                })
                ->limit($pag)
                ->get();
            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function fetchProductVariationData()
    {
        try {
            $pag = request('pag') ?? 50;
            $data = ProductVariation::query()
                ->with('product')
                ->where('is_active', true)
                ->when(request('search'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('name', 'LIKE', '%' . request('search') . '%');
                        $query->orWhere('sku', 'LIKE', '%' . request('search') . '%');
                        $query->orWhereHas('product', function ($product) {
                            $product->where('name_en', 'LIKE', '%' . request('search') . '%');
                            $product->orWhere('name_kh', 'LIKE', '%' . request('search') . '%');
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

    public function fetchProductAttributeData()
    {
        try {
            $pag = request('pag') ?? 50;
            $data = ProductAttribute::query()
                ->where('is_active', true)
                ->when(request('search'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('name', 'LIKE', '%' . request('search') . '%');
                        $query->orWhere('code', 'LIKE', '%' . request('search') . '%');
                        $query->orWhere('input_type', 'LIKE', '%' . request('search') . '%');
                    });
                })
                ->limit($pag)
                ->get();
            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function fetchProductSourceData()
    {
        try {
            $data = ProductSource::query()
                ->where('is_active', true)
                ->when(request('search'), fn($q) => $q->where('name_en', 'LIKE', '%' . request('search') . '%'))
                ->limit(50)
                ->get();
            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function fetchProductLocationData()
    {
        try {
            $data = ProductLocation::query()
                ->where('is_active', true)
                ->when(request('search'), fn($q) => $q->where('name_en', 'LIKE', '%' . request('search') . '%'))
                ->limit(50)
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
