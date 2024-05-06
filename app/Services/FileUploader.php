<?php

namespace App\Services;

use Exception;

class FileUploader
{
    public function storeFile($request, $type)
    {
        try {
            if ($request->hasFile($type)) {
                $file = $request->file($type);
                $filename = time() . '_' . $file->getClientOriginalName();
                $data = $file->storeAs('uploads', $filename, 'public'); // storing file in storage/app/public/uploads
                return $data;
            }
        } catch (Exception $e) {
            dd($e);
        }
        return '';
    }

}
