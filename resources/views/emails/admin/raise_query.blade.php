@component('mail::message')

@component('mail::panel')
User's Email: {{ $data['email'] }}  
username: {{  $data['username'] }}  
Query Type: {{ $data['query']['category'] }}  
Query: {{ $data['query']['description'] }}  

@endcomponent
@endcomponent