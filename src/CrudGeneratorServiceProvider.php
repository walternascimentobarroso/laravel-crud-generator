<?php

namespace WalterNascimentoBarroso\CrudGenerator;

use Illuminate\Support\ServiceProvider;

class CrudGeneratorServiceProvider extends ServiceProvider
{
    protected $commands = [
        'WalterNascimentoBarroso\CrudGenerator\Console\Commands\CommonCrud',
        'WalterNascimentoBarroso\CrudGenerator\Console\Commands\CrudInverse',
        'WalterNascimentoBarroso\CrudGenerator\Console\Commands\CrudMigration',
        'WalterNascimentoBarroso\CrudGenerator\Console\Commands\CrudRest',
        'WalterNascimentoBarroso\CrudGenerator\Console\Commands\DbSettings',
    ];

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    }

    public function register()
    {
        $this->commands($this->commands);
    }
}
