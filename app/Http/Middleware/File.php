<?php

namespace App\Http\Middleware;

use App\Traits\FilesystemTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class File
{
    use FilesystemTrait;

    public function handle(Request $request, Closure $next, string $context): Response
    {
        $disk = Storage::disk('backup');
        $filename = $request->route('filename');

        if (!is_null($filename)) {
            $filename = "$context/$filename";

            if ($disk->exists($filename)) {
                $oDateTime = \DateTime::createFromFormat('U', $disk->lastModified($filename));

                $request->attributes->set('fileinfo', collect([
                    'filename' => collect(explode('/', $filename))->last(),
                    'filepath' => $disk->path($filename),
                    'filesize' => $this->filesizeVerbose($disk->size($filename), 1),
                    'filetype' => $disk->mimeType($filename),
                    'lastModified' => $oDateTime->format('d/m/Y H:i:s'),
                ]));
            } else {
                abort(404);
            }
        }

        return $next($request);
    }
}
