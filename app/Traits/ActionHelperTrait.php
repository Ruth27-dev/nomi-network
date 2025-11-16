<?php

namespace App\Traits;

trait ActionHelperTrait
{
    protected function responseSuccess($data = null, $message = null)
    {
        return [
            'code'      => 200,
            'data'      => $data,
            'status'    => 'success',
            'error'     => false,
            'message'   => $message ?? 'Operation successful!',
        ];
    }

    protected function responseError($message = null)
    {
        return [
            'code'      => 500,
            'data'      => null,
            'status'    => 'error',
            'error'     => true,
            'message'   => $message ?? 'Something went wrong!',
        ];
    }

    protected function getAction($action)
    {
        switch ($action) {
            case 'create':
                return [
                    'key'   => config('dummy.action.create.key'),
                    'name'  => config('dummy.action.create.name'),
                ];
            case 'update':
                return [
                    'key'   => config('dummy.action.update.key'),
                    'name'  => config('dummy.action.update.name'),
                ];
            case 'update_status':
                return [
                    'key'   => config('dummy.action.update_status.key'),
                    'name'  => config('dummy.action.update_status.name'),
                ];
            case 'delete':
                return [
                    'key'   => config('dummy.action.delete.key'),
                    'name'  => config('dummy.action.delete.name'),
                ];
            case 'restore':
                return [
                    'key'   => config('dummy.action.restore.key'),
                    'name'  => config('dummy.action.restore.name'),
                ];
            case 'destroy':
                return [
                    'key'   => config('dummy.action.destroy.key'),
                    'name'  => config('dummy.action.destroy.name'),
                ];
        }
    }
}
