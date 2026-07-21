<div class="flex flex-col h-full -m-6">
    <x-slot:title>
        Ares AI | Vultify
    </x-slot>
    @php
        $aresSrc = 'https://ares.vultify.io/chat';
        if (session('ares_token')) {
            $aresSrc .= '?session=' . urlencode(session('ares_token'));
        }
    @endphp
    <iframe
        src="{{ $aresSrc }}"
        class="w-full border-0"
        style="height: calc(100vh - 4rem);"
        allow="microphone; autoplay; clipboard-write"
        title="Ares AI Agent"
    ></iframe>
</div>
