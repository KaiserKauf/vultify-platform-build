<div>
    <x-slot:title>
        System Logs | Vultify
    </x-slot>
    <div class="flex items-center justify-between">
        <h1>System-Logs</h1>
        <x-forms.button wire:click="refresh">Aktualisieren</x-forms.button>
    </div>
    <div class="subtitle">Recent Laravel log output — most recent line first.</div>

    <div class="flex flex-wrap gap-2 mb-4">
        @foreach ($files as $file)
            <button
                wire:click="selectFile('{{ $file }}')"
                class="px-3 py-1 text-sm rounded {{ $file === $activeFile ? 'bg-coollabs text-white' : 'dark:bg-coolgray-200 bg-neutral-200' }}"
            >{{ $file }}</button>
        @endforeach
        @if (count($files) === 0)
            <span class="text-sm text-neutral-500">Keine Log-Dateien gefunden.</span>
        @endif
    </div>

    <div class="p-4 overflow-auto rounded dark:bg-coolgray-100 bg-neutral-100" style="max-height: 70vh;">
        <pre class="text-xs whitespace-pre-wrap font-mono">@foreach (explode("\n", $tail) as $line)
@if (str_contains($line, '.ERROR') || str_contains($line, '.CRITICAL'))
<span class="text-error">{{ $line }}</span>
@elseif (str_contains($line, '.WARNING'))
<span class="text-warning">{{ $line }}</span>
@else
{{ $line }}
@endif
@endforeach</pre>
        @if (trim($tail) === '')
            <span class="text-sm text-neutral-500">Leer.</span>
        @endif
    </div>
</div>
