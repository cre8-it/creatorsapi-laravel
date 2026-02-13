<?php

namespace CreatorsApi\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class CreatorsApi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'creatorsapi';
    }
}
