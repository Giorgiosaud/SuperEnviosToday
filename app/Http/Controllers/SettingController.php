<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * @return View
     */
    public function index()
    {
        $settings = Setting::paginate();
        return view('coordinator.settings.index', compact('settings'));
    }
}
