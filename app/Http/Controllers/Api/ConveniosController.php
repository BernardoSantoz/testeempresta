<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Util;

class ConveniosController extends Controller
{
    public function getConvenios()
    {
        $loadedJson = Util::loadLocalJson('convenios.json');

        if (isset($loadedJson['error'])) {
            return response()->json(['error' => $loadedJson['error']['message']], $loadedJson['error']['code'], [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json($loadedJson, 200, [], JSON_UNESCAPED_UNICODE);
    }
}