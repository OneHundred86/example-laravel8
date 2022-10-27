<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class V1Controller extends Controller
{
    public function postParamsAndFiles(Request $request)
    {
        $forms = $request->all();
        $files = $request->allFiles();

        return [
            'params_key' => array_keys($forms),
            'files_key' => array_keys($files),
        ];
    }
}
