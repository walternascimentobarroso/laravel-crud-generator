<?php

namespace WalterNascimentoBarroso\CrudGenerator\Console\Commands;

use Illuminate\Support\Str;

class CrudRest extends CommonCrud
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:rest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD Rest';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $modulelower = Str::singular(strtolower($this->ask('What is your module name?')));
        $module = Str::studly($modulelower);

        $strFields = strtolower($this->ask('What is your fields name?'));
        $arrayFields = explode(",", $strFields);

        $this->makeModel($module, $arrayFields);
        \Artisan::call("make:resource {$module}Resource");
        \Artisan::call("make:migration create_{$modulelower}_table");
        $this->makeController($module);
        $this->makeRoutes(Str::plural($modulelower), $module);
        //php artisan migrate --seed
        //php artisan make:test UserTest --unit
    }
}
