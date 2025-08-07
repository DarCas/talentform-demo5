<?php

namespace App\Http\Controllers;

use App\Traits\FilesystemTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class AbstractBackupController extends Controller
{
    use FilesystemTrait;

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

    public function delete(Request $request)
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
            $disk->delete($request->attributes->get('fileinfo')->get('filepath'));
        }

        return redirect("/{$this->context}/backup");
    }

    public function download(Request $request)
    {
        return response()
            ->download($request->attributes->get('fileinfo')->get('filepath'));
    }

    private function items()
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
