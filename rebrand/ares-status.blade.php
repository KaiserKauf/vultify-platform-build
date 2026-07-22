<div class="flex items-center justify-between p-4 mb-2 border rounded dark:border-coolgray-200 dark:bg-coolgray-100">
    <div class="flex items-center gap-3">
        <div class="badge {{ $online ? 'badge-success' : 'badge-error' }}"></div>
        <div>
            <div class="font-bold">Ares AI</div>
            <div class="text-xs {{ $online ? 'text-success' : 'text-error' }}">
                @if ($online)
                    Online{{ $version ? " · v{$version}" : '' }}
                @else
                    Offline / nicht erreichbar
                @endif
            </div>
        </div>
    </div>
    @if ($online && $platformConfigured)
        <div class="flex items-center gap-2 text-xs">
            @if ($resourcesUnhealthy > 0)
                <span class="badge badge-warning">{{ $resourcesHealthy }}/{{ $resourcesTotal }} gesund</span>
            @else
                <span class="badge badge-success">{{ $resourcesTotal }} Ressourcen gesund</span>
            @endif
        </div>
    @endif
    <a href="/ares" wire:navigate class="text-xs underline">Öffnen &rarr;</a>
</div>
