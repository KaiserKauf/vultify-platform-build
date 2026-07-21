{{ Illuminate\Mail\Markdown::parse('---') }}

Thank you,<br>
{{ config('app.name') ?? 'Vultify' }}

{{ Illuminate\Mail\Markdown::parse('[Contact Support](mailto:info@aleiva.de)') }}
