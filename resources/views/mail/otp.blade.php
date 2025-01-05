
@component('mail::message')
Hi, **{{ $mailDetails['nickname'] }}**,  {{-- use double space for line break --}}

@component('mail::panel')
Here is your HappiMynd Verification code **{{ $mailDetails['otp'] }}** .   

Be Happi!  
OTP expires in 1hr  
@endcomponent

Team HappiMynd,  {{-- use double space for line break --}}
           {{-- use double space for line break --}}
@endcomponent
