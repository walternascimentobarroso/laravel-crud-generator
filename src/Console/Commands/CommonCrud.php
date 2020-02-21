<?php

namespace WalterNascimentoBarroso\CrudGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class CommonCrud extends Command
{
    // Criando Model
    public function makeModel($module, $table_name)
    {
        # get list of fields
        $arrayFieldsFull = Schema::getColumnListing($table_name);

        $arrayFields = array_diff($arrayFieldsFull, ["id", "created_at", "updated_at", "deleted_at"]);
        \Artisan::call("make:model Models/{$module}");
        $path_route = app_path().DIRECTORY_SEPARATOR.'Models'.DIRECTORY_SEPARATOR."{$module}.php";
        $model = __DIR__.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'model.txt';
        $fields = '[\''.implode("', '", $arrayFields).'\']';
        $output = str_replace('$CLASS$', $module, file_get_contents($model));
        $output = str_replace('$TABLE$', $table_name, $output);
        if (in_array("deleted_at", $arrayFieldsFull)) {
            $output = str_replace('$SOFTDELETE$', "use Illuminate\Database\Eloquent\SoftDeletes;\n", $output);
            $output = str_replace('$USESOFTDELETE$', "use SoftDeletes;", $output);
        } else {
            $output = str_replace('$SOFTDELETE$', "\n", $output);
            $output = str_replace('$USESOFTDELETE$', "\n", $output);
        }
        $output = str_replace('$FIELDS$', $fields, $output);
        File::put($path_route, $output);
    }

    // Criando Controller
    public function makeController($module)
    {
        \Artisan::call("make:controller API/{$module}Controller");
        $path_route = app_path().DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.'API'.DIRECTORY_SEPARATOR."{$module}Controller.php";
        $controller = __DIR__.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'controller.txt';
        $output = str_replace('$CLASS$', $module, file_get_contents($controller));
        File::put($path_route, $output);
    }

    // Criando Route
    public function makeRoutes($modulelower, $module)
    {
        $routes = "Route::apiResource('$modulelower', 'API\\{$module}Controller');";
        $path_route = base_path().DIRECTORY_SEPARATOR.'routes'.DIRECTORY_SEPARATOR.'apiroutes'.DIRECTORY_SEPARATOR.$modulelower.'.php';

        $apidoc = __DIR__.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'apidoc.txt';
        $output = str_replace('$MODULE$', $modulelower, file_get_contents($apidoc));

        $newoutput = "<?php\n\n".$output . $routes;
        File::put($path_route, $newoutput);
        // $path_route = base_path().DIRECTORY_SEPARATOR.'routes'.DIRECTORY_SEPARATOR.'api.php';
        // File::append($path_route, $routes);
        $this->info("Module $module created!");
        $this->info("1) <fg=white>List</>    Verb: <fg=yellow>GET</>,    URL: <fg=blue>http://localhost:8000/api/$modulelower</>");
        $this->info("2) <fg=white>Create</>  Verb: <fg=yellow>POST</>,   URL: <fg=blue>http://localhost:8000/api/$modulelower</>");
        $this->info("3) <fg=white>Show</>    Verb: <fg=yellow>GET</>,    URL: <fg=blue>http://localhost:8000/api/$modulelower/{id}</>");
        $this->info("4) <fg=white>Update</>  Verb: <fg=yellow>PUT</>,    URL: <fg=blue>http://localhost:8000/api/$modulelower/{id}</>");
        $this->info("5) <fg=white>Delete</>  Verb: <fg=yellow>DELETE</>, URL: <fg=blue>http://localhost:8000/api/$modulelower/{id}</>");
        $this->info("\n");
    }

    protected function createMigration($table, $schemaOut)
    {
        $allConfig =  [
            'CLASS' => "Create".Str::studly($table)."Table",
            'UP'    => $schemaOut,
            'DOWN'  => "Schema::dropIfExists('$table');"
        ];

        $this->replaceMigrationsWith($allConfig, file_get_contents($this->getTemplate('migration.txt')));
    }

    protected function replaceFieldsWith($schema, $template, $table)
    {
        $output = str_replace('$FIELDS$', $schema, $template);
        $output = str_replace('$TABLE$', $table, $output);
        return $output;
    }

    protected function getTemplate($file)
    {
        return __DIR__.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$file;
    }

    protected function replaceMigrationsWith($config, $template)
    {
        $output = str_replace('$CLASS$', $config['CLASS'], $template);
        $output = str_replace('$UP$', $config['UP'], $output);
        $output = str_replace('$DOWN$', $config['DOWN'], $output);
        $path_route = database_path().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR.date('Y_m_d_His').'_'.Str::snake($config['CLASS']).'.php';
        File::put($path_route, $output);
    }
}
