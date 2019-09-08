<?php

namespace Adams\Rcon\Test;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Adams\Rcon\Facades\Facade as RconFacade;
use Adams\Rcon\RconServiceProvider;

class TestCase extends OrchestraTestCase
{
    /**
     * Load package service provider
     * 
     * @param  \Illuminate\Foundation\Application $app
     * @return Adams\Rcon\RconServiceProvider
     */
    protected function getPackageProviders($app)
    {
        return [RconServiceProvider::class];
    }
    /**
     * Load package alias
     * 
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Rcon' => RconFacade::class,
        ];
    }
}