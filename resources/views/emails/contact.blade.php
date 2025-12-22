<x-mail::message>
# New Contact Form Submission

**From:** {{ $name }}

**Email:** [{{ $email }}](mailto:{{ $email }})

@if($company)
**Company:** {{ $company }}
@endif

## Message

{{ $message }}

<x-mail::button :url="'mailto:' . $email">
Reply to {{ $name }}
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
