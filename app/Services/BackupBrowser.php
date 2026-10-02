<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BackupBrowser
{
    public function root(): string
    {
        $configured = config('backups.root');

        return is_dir($configured) ? realpath($configured) : config('backups.fallback_root');
    }

    public function directory(string $relativePath = ''): array
    {
        $path = $this->resolve($relativePath);
        abort_unless(is_dir($path), 404);

        $items = collect(File::directories($path))
            ->map(fn (string $directory) => $this->item($directory, true))
            ->merge(collect(File::files($path))->map(fn (\SplFileInfo $file) => $this->item($file->getPathname(), false)))
            ->sortBy([
                ['is_directory', 'desc'],
                ['name', 'asc'],
            ])
            ->values();

        return [
            'path' => $this->normalize($relativePath),
            'items' => $items,
            'source' => is_dir(config('backups.root')) ? config('backups.root') : config('backups.fallback_root'),
        ];
    }

    public function file(string $relativePath): \SplFileInfo
    {
        $path = $this->resolve($relativePath);
        abort_unless(is_file($path), 404);

        return new \SplFileInfo($path);
    }

    public function preview(string $relativePath): array
    {
        $file = $this->file($relativePath);
        $maxBytes = config('backups.preview_max_kb') * 1024;
        $extension = strtolower($file->getExtension());
        $textual = in_array($extension, ['sql', 'txt', 'log', 'json', 'xml', 'csv', 'md']);

        if (!$textual) {
            return [
                'supported' => false,
                'message' => 'Preview tersedia untuk file teks seperti SQL, JSON, CSV, dan log.',
                'content' => null,
            ];
        }

        if ($file->getSize() > $maxBytes) {
            return [
                'supported' => false,
                'message' => 'File terlalu besar untuk preview. Silakan download file ini.',
                'content' => null,
            ];
        }

        return [
            'supported' => true,
            'message' => null,
            'content' => file_get_contents($file->getPathname()),
        ];
    }

    public function resolve(string $relativePath): string
    {
        $root = realpath($this->root());
        abort_unless($root !== false, 404);

        $relativePath = trim(str_replace('\\', '/', $relativePath), '/');
        $candidate = realpath($root . ($relativePath ? DIRECTORY_SEPARATOR . $relativePath : ''));

        abort_unless($candidate !== false && ($candidate === $root || str_starts_with($candidate, $root . DIRECTORY_SEPARATOR)), 404);

        return $candidate;
    }

    private function item(string $path, bool $isDirectory): array
    {
        $info = new \SplFileInfo($path);
        $relative = ltrim(str_replace('\\', '/', str_replace($this->root(), '', $path)), '/');

        return [
            'name' => $info->getFilename(),
            'path' => $relative,
            'is_directory' => $isDirectory,
            'extension' => $isDirectory ? null : strtolower($info->getExtension()),
            'size' => $isDirectory ? null : $info->getSize(),
            'size_label' => $isDirectory ? 'Folder' : $this->formatBytes($info->getSize()),
            'modified_at' => date('M j, Y · H:i', $info->getMTime()),
            'icon' => $isDirectory ? 'folder' : $this->iconFor($info->getExtension()),
        ];
    }

    private function normalize(string $path): string
    {
        return trim(str_replace('\\', '/', $path), '/');
    }

    private function iconFor(string $extension): string
    {
        return match (strtolower($extension)) {
            'sql' => 'database',
            'zip', 'gz', 'tar', '7z' => 'archive',
            'json', 'xml', 'csv', 'txt', 'log' => 'file-text',
            default => 'file',
        };
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1024 ** 2) return round($bytes / 1024, 1) . ' KB';
        if ($bytes < 1024 ** 3) return round($bytes / 1024 ** 2, 1) . ' MB';

        return round($bytes / 1024 ** 3, 1) . ' GB';
    }
}
