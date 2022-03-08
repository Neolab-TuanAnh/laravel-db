<?php

namespace PandaDev\LaravelDb\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelDb extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'lang_db';
    }
}