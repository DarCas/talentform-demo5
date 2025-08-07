<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class AbstractBackupController extends Controller
{
    protected string $context;
    protected string $title;

    public function index()
    {
        [$count, $items] = $this->items();

        $content = view('front.backup', [
            'context' => $this->context,
            'count' => $count,
            'items' => $items,
            'title' => $this->title,
        ]);

        return view('front.default', [
            'centered' => false,
            'content' => $content,
            'title' => $this->title,
            'q' => null,
            'user' => Session::get('logged_in'),
        ]);
    }

    public function delete(Request $request, string $filename = null)
    {
        /**
         * Mi collego al mio disco virtuale «backup» (vedi ~/config/filesystem.php)
         */
        $disk = Storage::disk('backup');

        if ($request->isMethod('post')) {
            /** @var array $files */
            $files = $request->post('files');

            foreach ($files as $file) {
                if ($disk->exists("{$this->context}/$file")) {
                    $disk->delete("{$this->context}/$file");
                }
            }
        } else if ($request->isMethod('get')) {
            if ($disk->exists("{$this->context}/$filename")) {
                $disk->delete("{$this->context}/$filename");
            }
        }

        return redirect("/{$this->context}/backup");
    }

    public function download(string $filename)
    {
        /**
         * Mi collego al mio disco virtuale «backup» (vedi ~/config/filesystem.php)
         */
        $disk = Storage::disk('backup');

        if ($disk->exists("{$this->context}/$filename")) {
            return response()
                ->download($disk->path("{$this->context}/$filename"));
        } else {
            return response()
                ->noContent(404);
        }
    }

    private function items()
    {
        $disk = Storage::disk('backup');

        $items = collect($disk->files("/{$this->context}"))
            ->filter(fn($file) => Str::endsWith($file, '.csv'))
            ->map(function ($file) use ($disk) {
                $oDateTime = \DateTime::createFromFormat('U', $disk->lastModified($file));

                return [
                    'filename' => substr($file, 6),
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

    private function filesizeVerbose(int $bytes, int $decimals = 2, bool $iec = false): string
    {
        if ($iec) {
            /**
             * Dimensioni con sistema binario
             */
            $size = ['B', 'kiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
        } else {
            /**
             * Dimensioni con sistema decimale
             */
            $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        }

        /**
         * Setto il dividendo in funzione del paramentro $iec
         * che mi indica se voglio il valore con il sistema decimale
         * o il sistema binario
         */
        $divider = $iec ? 1024 : 1000;

        $factor = 0;

        while ($bytes >= $divider) {
            $bytes /= $divider;

            ++$factor;
        }

        return number_format($bytes, $decimals) .
            " {$size[$factor]}";
    }
}
