<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\File;

class FileUploadController extends Controller
{
    public function creatForm() {
        return view('file-upload');
    }

    public function fileUpload(Request $req){
        $req->validate([
            'file' => 'required|mimes:csv,tvt,pdf|max:2048'
        ]);

        $fileModel = new File;

        if ($req->file()) {
            $fileName = time().'_' .$req->file->getOriginName();
            $filePath = $req->file('file')->storeAs('uploads',$fileName,'public');
            $fileModel->file_name = time().'_'.$req->file->getOriginName();
            $fileModel->file_path = '/storage/'.$filePath;
            $fileModel->save();

            return back()
                ->with('success','File has been uploaded successfully')
                ->with('file', $fileName);

        }
    }
}
