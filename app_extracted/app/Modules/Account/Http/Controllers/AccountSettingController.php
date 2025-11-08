<?php

namespace App\Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Account\Models\Account;
use App\Modules\Account\Http\Requests\SaveContactInformationRequest;
use App\Http\Controllers\Controller;

class AccountSettingController extends Controller
{
    //
    public function updateContact(SaveContactInformationRequest $request){
    	dd($request);
    }
}
