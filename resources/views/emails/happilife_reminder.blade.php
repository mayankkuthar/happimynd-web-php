@component('mail::message')
# Hi {{ $name ?: 'there' }},

{{ $reminderMessage }}

@if($progress['answered'] > 0)
**Your progress: {{ $progress['answered'] }} of {{ $progress['total'] }} questions answered ({{ $progress['percent'] }}%)**

Your answers are saved. You'll continue from question {{ $progress['answered'] + 1 }} — on the website or in the HappiMynd app, using the same login.
@endif

@component('mail::button', ['url' => $resumeUrl])
{{ $progress['answered'] > 0 ? 'Continue HappiLIFE' : 'Start HappiLIFE' }}
@endcomponent

Take it slow and answer honestly — there are no right or wrong answers.

Warmly,<br>
Team HappiMynd
@endcomponent
