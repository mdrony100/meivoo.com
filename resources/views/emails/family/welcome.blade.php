@component('mail::message')

# Welcome to {{ $family->name }}

Hello **{{ $user->name }}**,

Your account has been created successfully.

---

## 🔐 Login Credentials

- Email: {{ $user->email }}
- Password: {{ $password }}

---

## 👨‍👩‍👧 Family Information

You have been added to:

**{{ $family->name }}**

You can now login and view your family tree.

---

@component('mail::button', ['url' => url('/login')])
Login Now
@endcomponent

---

Thanks,
{{ config('app.name') }}

@endcomponent
