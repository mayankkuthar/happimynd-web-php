@component('mail::message')
# Hi {{ $name ?: 'there' }},

{{ $bodyText }}

@component('mail::table')
| | |
|:--|:--|
@foreach($details as $label => $value)
| **{{ $label }}** | {{ $value }} |
@endforeach
@endcomponent

Please join from the HappiMynd app a few minutes before the start time.

Warmly,<br>
Team HappiMynd
@endcomponent
