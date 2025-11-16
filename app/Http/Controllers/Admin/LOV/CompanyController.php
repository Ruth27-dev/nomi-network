<?php

namespace App\Http\Controllers\Admin\LOV;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompanyRequest;
use App\Models\ListOfValue;
use App\Models\UploadFile;
use App\Services\ActivityLogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    protected $module;
    public function __construct()
    {
        parent::__construct();
        $this->module = [
            'key'   => config('dummy.module.company.key'),
        ];
        $this->middleware('permission:company-view', ['only' => ['index', 'data']]);
        $this->middleware('permission:company-update', ['only' => ['save']]);
    }


    public function index()
    {
        $data['company'] = ListOfValue::where('type', 'company')->first();
        return view("admin::pages.setting.company.index", $data);
    }

    public function save(CompanyRequest $request)
    {
        DB::beginTransaction();
        try {
            $logo = UploadFile::uploadFile('/list-of-value', $request->file('logo'), $request->tmp_file);

            $input = [
                'type'    => $request->type,
                'image'   => $logo,
                'add_on'  => [
                    'name' => [
                        'en' => $request->name_en,
                        'km' => $request->name_km,
                    ],
                    'address' => [
                        'en' => $request->address_en,
                        'km' => $request->address_km,
                    ],
                    'phone' => [
                        'en' => $request->phone_en,
                        'km' => $request->phone_km,
                    ],
                    'vat_tin' => $request->vat_tin,
                ],
                'status'  => $request->status,
                'user_id' => Auth::user()->id,
            ];

            if (!$request->id) {
                $data = ListOfValue::create($input);
            } else {
                $data = ListOfValue::find($request->id);
                if ($request->hasFile('logo') || !$request->tmp_file) {
                    UploadFile::deleteFile('/list-of-value', $data->image);
                }
                $data->update($input);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => $request->id
                    ? __('form.message.update.success')
                    : __('form.message.create.success'),
                'error' => false,
                'id' => $data->id,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => __('form.message.error'),
                'error' => true,
                'exception' => $e->getMessage(),
            ]);
        }
    }
}