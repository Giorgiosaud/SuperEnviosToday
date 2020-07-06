<?php

namespace App\Http\Controllers\Api;

use App\Events\CreatedSetting;
use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SettingController extends Controller
{
    public function index()
    {
        $request = request();
        $currency = Setting::query();
        if ($request->has('key')) {
            $currency->where('key', 'like', '%' . $request->key . '%');
        }

        return $request->has('perPage') ? $currency->paginate($request->perPage) : $currency->paginate();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function update(Setting $setting, Request $request)
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:255'],
            'value' => ['required', 'string', 'max:255'],
        ]);
        return $setting->update($data);
    }

    /**
     * @param Setting $setting
     * @return Response
     * @throws \Exception
     */
    public function destroy(Setting $setting)
    {
        $setting->delete();
        return response('setting deleted', Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response $response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:255'],
            'value' => ['required', 'string', 'max:255'],
        ]);
        $setting = Setting::create($data);
        event(new CreatedSetting($setting));
        return response('setting created', Response::HTTP_CREATED);
    }
    //
}
