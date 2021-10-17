@component('mail::message')
# Здравейте, {{ $user->name }}

Искаме да ви уведомим, че има промени по договора на ЕГН/Булстат {{ $item->value }}. Моможе да разгледате промените в системата.

@component('mail::button', ['url' => 'https://collect.fitsys.com'])
Виж в системата
@endcomponent

Поздрави,<br>
система Collect
@endcomponent