<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class AresStatus extends Component
{
    public bool $online = false;

    public string $version = '';

    public function mount()
    {
        $status = Cache::remember('ares_status_widget', 30, function () {
            try {
                $res = Http::timeout(3)->get('https://ares.vultify.io/api/health');
                if ($res->successful()) {
                    return ['online' => true, 'version' => (string) ($res->json('version') ?? '')];
                }
            } catch (\Throwable $e) {
                // Ares unreachable — widget just shows offline, no error surfaced to the user.
            }
            return ['online' => false, 'version' => ''];
        });
        $this->online = $status['online'];
        $this->version = $status['version'];
    }

    public function render()
    {
        return view('livewire.ares-status');
    }
}
