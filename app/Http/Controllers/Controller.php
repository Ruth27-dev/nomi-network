<?php

namespace App\Http\Controllers;

use App\Models\ListOfValue;
use App\Traits\ActionHelperTrait;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use ActionHelperTrait;
    public $user, $active, $isRoleSuperAdmin, $inactive, $company;
    public function __construct()
    {
        $this->active           = config('dummy.status.active.key');
        $this->inactive         = config('dummy.status.inactive.key');
        $this->isRoleSuperAdmin = config('dummy.user.role.super_admin');
        $this->company          = ListOfValue::where('type', 'company')->first();
        view()->share(['company' => $this->company]);
    }

    public function onCheckConditionSearch($value)
    {
        if ($value == null || $value == 'null') {
            return '';
        }
        if (empty($value)) {
            return '';
        }
        return $value;
    }
}
