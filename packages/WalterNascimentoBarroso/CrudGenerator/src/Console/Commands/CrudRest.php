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

        $this->makeMigration($modulelower, $arrayFields);
        $this->makeModel($module, $arrayFields);
        \Artisan::call("make:resource {$module}Resource");
        $this->makeController($module);
        $this->makeRoutes(Str::plural($modulelower), $module);
        //php artisan migrate --seed
        //php artisan make:test UserTest --unit
    }

    public function makeMigration($module, $arrayFields)
    {
        $schema = '';
        foreach ($arrayFields as $field) {
            $schema .= $this->generateField(trim($field));
        }
        $schema = $this->generateFieldExtra($schema);
        $schemaOut = $this->replaceFieldsWith($schema, file_get_contents($this->getTemplate('schema.txt')), $module);
        $this->createMigration($module, $schemaOut);
    }

    protected function generateField($field)
    {
        return "\t\t\t\$table->string('$field');\n";
    }

    protected function generateFieldExtra($schema)
    {
        $newSchema = "\t\t\t\$table->bigIncrements('id');\n";
        $newSchema .= $schema;
        $newSchema .= "\t\t\t\$table->timestamps();\n";
        $newSchema .= "\t\t\t\$table->softDeletes();\n";
        return $newSchema;
    }
}
