<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Support\Facades\Validator;

use App\Exceptions\BadRequestException;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $POST) {

        $credentials = $POST->all();

        $validator = Validator::make($credentials,[
            'email' => ['required', 'email'],
            'password' => ['required'],
        ],[
            'required' => 'Полето :attribute е задължително',
            'email.email' => 'Имейла не е валиден. Пример за валиден имейл - test@example.com'            
        ],[
            'email' => 'Имейл',
            'password' => 'Парола'
        ]);

        if($validator->fails()) {
            throw new BadRequestException(implode(',',$validator->errors()->all()));
        }

        $remember = true;
        if(Auth::attempt($credentials, $remember)) {
            return [
                'user' => Auth::user()
            ];
        }

        throw new BadRequestException('Грешно потребителско име или парола');
    }

    public function logout()
    {
        Auth::logout();   
    }
}
