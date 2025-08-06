<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait ParamConverterTrait
{
    protected function getEntity(Request $request, string $paramName)
    {
        return $request->attributes->get($paramName);
    }
}
