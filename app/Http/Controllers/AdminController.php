<?php

namespace App\Http\Controllers;

use Auth;
use Hash;
use Validator;

use App\User;

use App\Exceptions\BadRequestException;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function show(Request $GET) {
        $user = Auth::user();
        return User::get()->filter(function($admin) use($user) {
            return $user->is_admin === 1 || $user->id === $admin->id;
        })->map(function($admin){
            return [
                'id' => $admin->id,
                'is_admin' => $admin->is_admin,
                'name' => $admin->name,
                'email' => $admin->email,
                'position' => $admin->position,
                'additional_identifier' => $admin->additional_identifier,
            ];
        });
    }

    public function store(Request $POST)
    {

        $this->validateSave($POST);

        $admin = User::findOrNew($POST['id']);
        $input = $POST->all();
        if(isset($input['password'])) {
            if(!isset($input['repeatPassword']) || $input['password'] !== $input['repeatPassword']) {
                throw new BadRequestException('Паролите не съвпадат');
            }

            if(strlen($input['password']) < 8) {
                throw new BadRequestException('Паролата трябва да бъде минимум 8 символа');
            }

            $admin->password = Hash::make($input['password']);
        } else if($POST['id'] === 0) {
            throw new BadRequestException('При създаване на нов акаунт паролата е задължителна');
        }
        
        $admin->is_admin = isset($input['is_admin']) ? $input['is_admin'] : 0;
        $admin->name = $input['name'];
        $admin->email = $input['email'];
        $admin->position = $input['position'];
        $admin->additional_identifier = $input['additional_identifier'];
        $admin->save();

    }

    private function validateSave(Request $request)
    {
        if($request['id'] === 0) {
            $uniqueRule = 'unique:users,email,NULL,deleted_at,deleted_at,NULL';
        } else {
            $uniqueRule = 'unique:users,email,'.$request['id'];
        }

        $validator = Validator::make($request->all(), $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email|'.$uniqueRule,
        ],$messages = [
            'required' => 'Полето :attribute е задължително',
            'name.min' => 'Името трябва да бъде поне 3 символа',
            'email.email' => 'Имейла не е валиден. Пример за валиден имейл - test@example.com',
            'email.unique' => 'Този имейл се използва от друг администратор'
        ],$fields = [
            'name' => 'Име',
            'email' => 'Имейл'
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();
            throw new BadRequestException(implode(',',$errors->all()));
        }
    }
}
