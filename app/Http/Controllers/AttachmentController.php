<?php

    namespace App\Http\Controllers;

    use App\Attachment;
    use Illuminate\Http\Request;

    class AttachmentController extends Controller
    {
        public function save(Request $request)
        {
            $request->validate([
                'file' => 'required|file|mimes:jpeg,bmp,png,gif,svg,pdf',
            ]);
            $file = request()->file('file');
            $name = rand(1, 999) . $file->getClientOriginalName();
            $path = "/uploads/" . date("Y") . '/' . date("m") . "/" . $name;
            $file->storeAs('uploads/' . date("Y") . '/' . date("m") . '/', $name, ['disk' => 'public']);
            return Attachment::create(['name' => $name, 'path' => $path, 'extension' => $file->getClientOriginalExtension()]);
            //
        }
    }
