<?php

namespace App\Services;

use App\Models\ListOfValue;
use App\Models\UploadFile;
use App\Traits\ActionHelperTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListOfValueService
{
    use ActionHelperTrait;

    public function data($type = null)
    {
        try {
            $pag = request('pag') ?? 50;
            $data = ListOfValue::query()
                ->with('branch:id,title')
                ->when(request('status'), function ($q) {
                    $q->where('status', request('status'));
                })
                ->where('type', $type)
                ->when(request('trash'), function ($q) {
                    $q->onlyTrashed();
                })
                ->when(request('search'), function ($q) {
                    $q->where(function ($q) {
                        $q->where('title->en', 'LIKE', '%' . request('search') . '%');
                        $q->orWhere('title->km', 'LIKE', '%' . request('search') . '%');
                    });
                })
                ->orderByDesc('sequence')
                ->paginate($pag);

            return $data;
        } catch (\Exception $th) {
            return $this->responseError();
        }
    }

    public function createOrUpdate($request, $add_on, $module)
    {
        DB::beginTransaction();
        try {
            $id = $request->id;
            $data = null;
            $image = UploadFile::uploadFile('/list-of-value', $request->file('image'), $request->tmp_file);
            $items = [
                'branch_id'     => $request->branch_id,
                'code'          => $request->code,
                'type'          => $request->type,
                'title'         => [
                    'en' => $request->title_en,
                    'km' => $request->title_km,
                ],
                'description'   => [
                    'en' => $request->description_en,
                    'km' => $request->description_km,
                ],
                'status'        => $request->status,
                'add_on'        => count($add_on) ? $add_on : null,
                'sequence'      => $request->sequence,
                'image'         => $image,
                'user_id'       => Auth::id(),
            ];

            if ($id) {
                $data = ListOfValue::find($id);
                $original_data = $data->getOriginal();
                $data->update($items);
            } else {
                $data = ListOfValue::create($items);
                $original_data = $data->getOriginal();
            }

            DB::commit();
            return $data;
        } catch (\Exception $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function detail($id, $relation = null){
        try {
            $data = ListOfValue::query()
                ->when($relation, function($q) use ($relation){
                    $q->with($relation);
                })
                ->findOrFail($id);
            return $this->responseSuccess($data);
        } catch (\Exception $e) {
            return $this->responseError();
        }
    }

    public function updateStatus($id, $status, $module)
    {
        try {
            $data = ListOfValue::findOrFail($id);
            $original_data = $data->getOriginal();
            $data->update([
                'status' => $status
            ]);
            return $this->responseSuccess(null, __('form.message.update.success'));
        } catch (\Exception $e) {
            return $this->responseError();
        }
    }

    public function delete($id, $module)
    {
        try {
            $data = ListOfValue::findOrFail($id);
            $original_data = $data->getOriginal();
            $data->delete();
            return $this->responseSuccess(null, __('form.message.delete.success'));
        } catch (\Exception $e) {
            return $this->responseError();
        }
    }

    public function restore($id, $module)
    {
        try {
            $data = ListOfValue::withTrashed()->findOrFail($id);
            $original_data = $data->getOriginal();
            $data->restore();
            return $this->responseSuccess(null, __('form.message.restore.success'));
        } catch (\Exception $e) {
            return $this->responseError();
        }
    }
}
