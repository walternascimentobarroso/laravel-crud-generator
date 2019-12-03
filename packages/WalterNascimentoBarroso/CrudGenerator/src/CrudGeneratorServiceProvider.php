<?php

namespace WalterNascimentoBarroso\CrudGenerator;

use Illuminate\Support\ServiceProvider;

class CrudGeneratorServiceProvider extends ServiceProvider
{
    protected $commands = [
        'WalterNascimentoBarroso\CrudGenerator\Console\Commands\CrudInverse',
        'WalterNascimentoBarroso\CrudGenerator\Console\Commands\CrudMigration',
        'WalterNascimentoBarroso\CrudGenerator\Console\Commands\CrudRest',
    ];

    public function boot()
    {
        $this->publishes([
            __DIR__.'/Http/Controllers/API' => app_path('Http/Controllers/API'),
        ]);
    }

    public function register()
    {
        $this->commands($this->commands);
    }
}
