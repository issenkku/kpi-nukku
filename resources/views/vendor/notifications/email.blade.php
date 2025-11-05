@component('mail::message')
@foreach ($introLines as $line)
{{ $line }}
@endforeach

@isset($actionText)
@component('mail::button', ['url' => $actionUrl, 'color' => $color ?? 'primary'])
{{ $actionText }}
@endcomponent
@endisset

@foreach ($outroLines as $line)
{{ $line }}
@endforeach

@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Regards'),<br>
{{ config('app.name') }}
@endif

{{-- Note: intentionally removed the default subcopy that shows the raw URL --}}
@endcomponent

