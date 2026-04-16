<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\File;

class FileUploadController extends BaseController
{
    public function fileUpload(Request $req): JsonResponse
    {
        $req->validate([
            'file' => 'required|mimes:csv,txt,pdf|max:2048'
        ]);

            $fileName = time() .'_' . $req->file('file')->getClientOriginalName();
            $filePath = $req->file('file')->storeAs('uploads',$fileName,'public');

            $file = File::create([
                'file_name' => $fileName,
                'file_path' => '/storage/'.$filePath
            ]);

            return response()->json([
                'message' => 'File has been uploaded successfully',
                'file' => $file
            ], 201);
    }
}
