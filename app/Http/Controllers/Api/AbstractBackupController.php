<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\FilesystemTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

abstract class AbstractBackupController extends Controller
{
    use FilesystemTrait;

    protected string $context;

    public function read()
    {
        [$count, $items] = $this->items();

        return response()
            ->json([
                'count' => $count,
                'items' => $items,
            ]);
    }

    public function delete(Request $request): mixed
    {
        /**
         * Mi collego al mio disco virtuale «backup» (vedi ~/config/filesystem.php)
         */
        $disk = Storage::disk('backup');

        $disk->delete("/{$this->context}/{$request->attributes->get('fileinfo')->get('filename')}");

        return response()
            ->json([
                'success' => true,
            ]);
    }

    public function download(Request $request): BinaryFileResponse
    {
        return response()
            ->download($request->attributes->get('fileinfo')->get('filepath'));
    }

    private function items(): array
    {
        $disk = Storage::disk('backup');

        $items = collect($disk->files("/{$this->context}"))
            ->filter(fn($file) => Str::endsWith($file, '.csv'))
            ->map(function ($file) use ($disk) {
                $oDateTime = \DateTime::createFromFormat('U', $disk->lastModified($file));

                return [
                    'filename' => collect(explode('/', $file))->last(),
                    'filesize' => $this->filesizeVerbose($disk->size($file), 1),
                    'filetype' => $disk->mimeType($file),
                    'lastModified' => $oDateTime->format('d/m/Y H:i:s'),
                ];
            })
            ->sortBy(fn($file) => $file['lastModified'], SORT_DESC, true);

        return [
            $items->count(),
            $items->values(),
        ];
    }
}
