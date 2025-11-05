@component('mail::message')
@foreach ($introLines as $line)
{{ $line }}
@endforeach

@isset($actionText)
{{ strip_tags($actionText) }}: <{{ $actionUrl }}>
@endisset

@foreach ($outroLines as $line)
{{ $line }}
@endforeach

@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Regards'),
{{ config('app.name') }}
@endif

{{-- Plain text version without the default troubleshooting subcopy --}}
@endcomponent

