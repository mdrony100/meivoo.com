@component('mail::message')
# New Contact Form Submission

You have received a new contact message.

**Name:** {{ $contact->name }}

**Email:** {{ $contact->email }}

**Subject:** {{ $contact->subject }}

@component('mail::button', ['url' => route('admin.contact.show', $contact->id)])
View Message
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent