@component('mail::message')
# Здравейте, {{ $user->name }}

Искаме да ви уведомим, че договора на ЕГН/Булстат {{ $item->value }} беше прекратен.

@component('mail::button', ['url' => 'https://collect.fitsys.com'])
Виж в системата
@endcomponent

Поздрави,<br>
система Collect
@endcomponent
