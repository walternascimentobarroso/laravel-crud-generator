<?php

namespace WalterNascimentoBarroso\CrudGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CommonCrud extends Command
{
    // Criando Model
    public function makeModel($module, $arrayFields)
    {
        \Artisan::call("make:model Models/{$module}");
        $path_route = app_path().DIRECTORY_SEPARATOR.'Models'.DIRECTORY_SEPARATOR."{$module}.php";
        $model = __DIR__.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'model.txt';
        $fields = '[\''.implode("', '", $arrayFields).'\']';
        $output = str_replace('$CLASS$', $module, file_get_contents($model));
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
        $path_route = base_path().DIRECTORY_SEPARATOR.'routes'.DIRECTORY_SEPARATOR.'api.php';
        $routes = "\nRoute::apiResource('$modulelower', 'API\\{$module}Controller');";
        File::append($path_route, $routes);
        $this->info("Module $module created!");
        $this->info("1) <fg=white>List</>    Verb: <fg=yellow>GET</>,    URL: <fg=blue>http://localhost:8000/api/$modulelower</>");
        $this->info("2) <fg=white>Create</>  Verb: <fg=yellow>POST</>,   URL: <fg=blue>http://localhost:8000/api/$modulelower</>");
        $this->info("3) <fg=white>Show</>    Verb: <fg=yellow>GET</>,    URL: <fg=blue>http://localhost:8000/api/$modulelower/{id}</>");
        $this->info("4) <fg=white>Update</>  Verb: <fg=yellow>PUT</>,    URL: <fg=blue>http://localhost:8000/api/$modulelower/{id}</>");
        $this->info("5) <fg=white>Delete</>  Verb: <fg=yellow>DELETE</>, URL: <fg=blue>http://localhost:8000/api/$modulelower/{id}</>");
        $this->info("\n");
    }
}
