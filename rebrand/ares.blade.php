<div class="flex flex-col h-full -m-6">
    <x-slot:title>
        Ares AI | Vultify
    </x-slot>
    @php
        $aresSrc = 'https://ares.vultify.io/chat';
        // URL fragment, not a query param: fragments are never sent to the
        // server or logged in access/Referer logs, unlike ?session=. Matches
        // the same handoff pattern used on vultify.io's own login flow.
        if (session('ares_token')) {
            $aresSrc .= '#session=' . urlencode(session('ares_token'));
        }
    @endphp
    @unless (session('ares_token'))
        <div class="p-3 text-sm border rounded mb-3 dark:border-coolgray-200 dark:bg-coolgray-100">
            Du bist nicht über "Login mit Ares" angemeldet, daher startet der Chat unten ohne
            automatische Anmeldung. Melde dich einmalig direkt im Fenster an, oder logge dich
            das nächste Mal über die Ares-SSO-Option ein, um das zu überspringen.
        </div>
    @endunless
    <iframe
        src="{{ $aresSrc }}"
        class="w-full border-0"
        style="height: calc(100vh - 4rem);"
        allow="microphone; autoplay; clipboard-write"
        title="Ares AI Agent"
    ></iframe>
</div>
