<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ParamsConverter
{
    public function handle(
        Request $request,
        Closure $next,
        string $model,
        string $paramName
    ): Response
    {
        $params = collect($request->route()->parameters());

        if ($params->has('id')) {
            $entity = $model::find($params->get('id'));

            if (is_null($entity)) {
                return \response()
                    ->noContent(400);
            }

            $request->attributes->set(
                $paramName,
                $model::find($params->get('id'))
            );
        }

        return $next($request);
    }
}
