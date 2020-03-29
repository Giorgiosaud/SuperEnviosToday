<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('coordinator/settings');
    }

    public function all()
    {
        return Setting::all();
    }

    public function setTax(Request $request)
    {
        $value = $request->value;
        $set = Setting::where('key', 'venezuelanBankTax')->first();
        $set['value'] = $value;
        $set->save();
    }
}
