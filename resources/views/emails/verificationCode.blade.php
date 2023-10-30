
<x-mail::message>
# Introduction

Dear {{$client->email}},
Your code is:{{$verificationCode}},


Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

