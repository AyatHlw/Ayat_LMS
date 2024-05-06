<?php

namespace App\Services;

class FileUploader
{
    public function storeFile($request, $type)
    {
        $data = '';
        if ($request->hasFile($type)) {
            $file = $request->file($type);
            $filename = time() . '_' . $file->getClientOriginalName();
            $data = $file->storeAs('uploads', $filename, 'public'); // storing file in storage/app/public/uploads
        }
        return $data;
    }
}
