<?php

namespace App\Livewire;

use Livewire\Component;

class SystemLogs extends Component
{
    public string $activeFile = 'laravel.log';

    public array $files = [];

    public string $tail = '';

    public function mount()
    {
        $this->refreshFiles();
        $this->loadTail();
    }

    public function refreshFiles(): void
    {
        $dir = storage_path('logs');
        $this->files = collect(glob("{$dir}/*.log"))
            ->map(fn ($p) => basename($p))
            ->sortDesc()
            ->values()
            ->all();
        if (! in_array($this->activeFile, $this->files) && count($this->files) > 0) {
            $this->activeFile = $this->files[0];
        }
    }

    public function selectFile(string $file): void
    {
        $this->activeFile = $file;
        $this->loadTail();
    }

    public function refresh(): void
    {
        $this->refreshFiles();
        $this->loadTail();
    }

    private function loadTail(int $lines = 300): void
    {
        $path = storage_path('logs/'.basename($this->activeFile));
        if (! is_file($path)) {
            $this->tail = '';

            return;
        }
        // Reverse-tail: read the file, keep the last N lines, most recent first.
        $content = file_get_contents($path);
        $allLines = preg_split('/\R/', trim($content));
        $this->tail = implode("\n", array_reverse(array_slice($allLines, -$lines)));
    }

    public function render()
    {
        return view('livewire.system-logs');
    }
}
