<?php

namespace App\Http\Controllers;

use App\Attachment;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function save(Request $request)
    {
        $file = request()->file('file');
        $name = rand(1, 999) . $file->getClientOriginalName();
        $path = "/uploads/" . date("Y") . '/' . date("m") . "/" . $name;
        $file->storeAs('uploads/' . date("Y") . '/' . date("m") . '/', $name, ['disk' => 'public']);
        return Attachment::create(['name' => $name, 'path' => "/storage$path", 'extension' => $file->getClientOriginalExtension()]);
        //
    }
}
