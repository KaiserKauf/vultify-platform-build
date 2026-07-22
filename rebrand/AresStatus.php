<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class AresStatus extends Component
{
    public bool $online = false;

    public string $version = '';

    public bool $platformConfigured = false;

    public int $resourcesTotal = 0;

    public int $resourcesHealthy = 0;

    public int $resourcesUnhealthy = 0;

    public function mount()
    {
        $status = Cache::remember('ares_status_widget', 30, function () {
            $out = ['online' => false, 'version' => '', 'platform_configured' => false, 'total' => 0, 'healthy' => 0, 'unhealthy' => 0];
            try {
                $res = Http::timeout(3)->get('https://ares.vultify.io/api/health');
                if ($res->successful()) {
                    $out['online'] = true;
                    $out['version'] = (string) ($res->json('version') ?? '');
                }
            } catch (\Throwable $e) {
                // Ares unreachable — widget just shows offline, no error surfaced to the user.
                return $out;
            }
            try {
                // Aggregate counts only (no names/uuids/domains) — this is an
                // unauthenticated endpoint by design, see Ares'
                // /api/platform/summary/public docstring for why: giving
                // this server-side widget an Ares admin token would mean a
                // standing privileged credential baked into this container.
                $platform = Http::timeout(3)->get('https://ares.vultify.io/api/platform/summary/public');
                if ($platform->successful() && $platform->json('configured')) {
                    $summary = $platform->json('summary') ?? [];
                    $out['platform_configured'] = true;
                    $out['total'] = (int) ($summary['total'] ?? 0);
                    $out['healthy'] = (int) ($summary['healthy'] ?? 0);
                    $out['unhealthy'] = (int) ($summary['unhealthy'] ?? 0);
                }
            } catch (\Throwable $e) {
                // Platform summary optional — health ping above already succeeded.
            }
            return $out;
        });
        $this->online = $status['online'];
        $this->version = $status['version'];
        $this->platformConfigured = $status['platform_configured'];
        $this->resourcesTotal = $status['total'];
        $this->resourcesHealthy = $status['healthy'];
        $this->resourcesUnhealthy = $status['unhealthy'];
    }

    public function render()
    {
        return view('livewire.ares-status');
    }
}
