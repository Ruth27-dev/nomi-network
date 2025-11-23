<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Models\UploadFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:category-view', ['only' => ['index', 'detail', 'data']]);
        $this->middleware('permission:category-create', ['only' => ['save']]);
        $this->middleware('permission:category-update', ['only' => ['save']]);
        $this->middleware('permission:category-delete', ['only' => ['delete']]);
        $this->middleware('permission:category-restore', ['only' => ['restore']]);
    }

    public function index()
    {
        return view('admin::pages.category.index');
    }

    public function data()
    {
        try {
            $pag = request('pag') ?? 50;
            $data = Category::query()
                ->when(request('status'), fn($q) => $q->where('status', request('status')))
                ->when(request('trash'), fn($q) => $q->onlyTrashed())
                ->when(request('search'), function ($q) {
                    $q->where(function ($q) {
                        $q->where('code', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('title->en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('description->en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('description->km', 'LIKE', '%' . request('search') . '%');
                    });
                })
                ->orderByDesc('created_at')
                ->paginate($pag);
            return $data;
        } catch (Exception $e) {
            return $this->responseError();
        }
    }
    public function save(CategoryRequest $request)
    {
        DB::beginTransaction();
        try {
            $image = UploadFile::uploadFile('/category', $request->file('image'));
            $input = [
                'title'         => [
                    'en'    => $request->title_en,
                    'km'    => $request->title_km,
                ],
                'description'   => [
                    'en'    => $request->description_en,
                    'km'    => $request->description_km,
                ],
                'slug'          => $request->slug,
                'status'        => $request->status,
                'image'         => $image,
                'sequence'      => $request->sequence,
                'user_id'       => Auth::user()->id,
            ];
            if (!$request->id) {
                $data = Category::create($input);
            } else {
                $data = Category::findOrFail($request->id);
                if ($request->file('image') || !$request->tmp_file) {
                    UploadFile::deleteFile('/category', $data->image);
                }
                $input['image'] = $image ?? $request->tmp_file;
                $data->update($input);
            }
            DB::commit();
            return $this->responseSuccess();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError();
        }
    }

    public function detail()
    {
        try {
            $data = Category::findOrFail(request('id'));
            return $this->responseSuccess($data);
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function updateStatus()
    {
        DB::beginTransaction();
        try {
            $data = Category::findOrFail(request('id'));
            $data->update([
                'status' => request('status'),
            ]);
            DB::commit();
            return $this->responseSuccess(null, __('form.message.update.success'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError();
        }
    }

    public function delete()
    {
        DB::beginTransaction();
        try {
            $data = Category::findOrFail(request('id'));
            $data->delete();
            DB::commit();
            return $this->responseSuccess(null, __('form.message.delete.success'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError();
        }
    }

    public function restore()
    {
        DB::beginTransaction();
        try {
            $data = Category::withTrashed()->findOrFail(request('id'));
            $data->restore();
            DB::commit();
            return $this->responseSuccess(null, __('form.message.restore.success'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError();
        }
    }

    public function sequence()
    {
        try {
            $data['max_ordering'] = Category::max('sequence') + 1;
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
