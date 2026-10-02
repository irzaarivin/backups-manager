<?php

namespace App\Http\Controllers;

use App\Services\BackupBrowser;
use Illuminate\Http\Request;

class FinderController extends Controller
{
    public function __construct(private readonly BackupBrowser $browser)
    {
    }

    public function index(Request $request)
    {
        $path = (string) $request->query('path', '');
        $listing = $this->browser->directory($path);

        return view('finder.index', [
            'listing' => $listing,
            'query' => (string) $request->query('q', ''),
        ]);
    }

    public function preview(string $path)
    {
        return response()->json([
            'name' => basename($path),
            ...$this->browser->preview($path),
        ]);
    }

    public function download(string $path)
    {
        $file = $this->browser->file($path);

        return response()->download($file->getPathname(), $file->getFilename());
    }
}
