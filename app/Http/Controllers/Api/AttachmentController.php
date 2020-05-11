<?php

namespace App\Http\Controllers\Api;

use App\Attachment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function upload(Request $request)
    {
        $file = request()->file('file');
        $name = rand(1, 999).$file->getClientOriginalName();
        $path = '/uploads/'.date('Y').'/'.date('m').'/'.$name;
        $file->storeAs('uploads/'.date('Y').'/'.date('m').'/', $name, ['disk' => 'public']);

        return Attachment::create(['name' => $name, 'path' => "/storage$path", 'extension' => $file->getClientOriginalExtension()]);
    }

    //
}
