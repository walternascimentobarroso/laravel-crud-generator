<?php

namespace WalterNascimentoBarroso\CrudGenerator\Console\Commands;

use Illuminate\Support\Str;
use WalterNascimentoBarroso\CrudGenerator\Console\Commands\DbSettings;

class CrudInverse extends CommonCrud
{
    protected $dbSettings;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:inverse {--table=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate api resource';

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
        $table = $this->option('table');
        if ($table) {
            $this->makeModule($table);
        } else {
            $this->dbSettings = new DbSettings;
            $tables = $this->dbSettings->getTables();
            $tablesExcluded = ["users", "migrations", "oauth_auth_codes", "oauth_access_tokens", "oauth_refresh_tokens", "oauth_clients", "oauth_personal_access_clients", "password_resets"];
            foreach ($tables as $tableName) {
                if (in_array($tableName, $tablesExcluded)) {
                    continue;
                }
                $this->makeModule($tableName);
            }
        }
    }

    private function makeModule($package)
    {
        $packageLower = strtolower($package);
        $module = Str::studly(Str::singular($packageLower));
        $this->makeModel($module, $packageLower);
        \Artisan::call("make:resource {$module}Resource");
        \Artisan::call("crud:migration --table=$packageLower");
        \Artisan::call("make:test {$module}Test --unit");
        \Artisan::call("make:seeder {$module}TableSeeder");
        \Artisan::call("make:factory {$module}Factory --model=Models/{$module}");
        $this->makeController($module);
        $this->makeRoutes(Str::plural($packageLower), $module);
    }
}
