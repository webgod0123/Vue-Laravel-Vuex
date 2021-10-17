<?php 

namespace App\Facades;
	
use Illuminate\Support\Facades\Facade;

class Boolean extends Facade {

    protected static function getFacadeAccessor() 
    {
         return 'App\Facades\BooleanHelper'; 
    }
}