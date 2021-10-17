@component('mail::message')
# Здравейте, {{ $user->name }}

Искаме да ви уведомим, че ЕГН/Булстат {{ $item->value }} има нов договор.

@component('mail::button', ['url' => 'https://collect.fitsys.com'])
Виж в системата
@endcomponent

Поздрави,<br>
система Collect
@endcomponent
