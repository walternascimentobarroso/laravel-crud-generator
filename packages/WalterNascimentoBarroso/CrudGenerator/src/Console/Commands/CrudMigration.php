<?php

namespace WalterNascimentoBarroso\CrudGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use DB;

class CrudMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:migration {--table=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Migration by SGBD';

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
        $tables = $this->option('table');
        if (!$tables) {
            $this->dbSettings = new DbSettings;
            $tables = $this->dbSettings->getTables();
            $tables = $this->removeExcludedTables($tables->toArray());
        }

        $this->generateTablesAndIndices($tables);
    }

    protected function generateTablesAndIndices($tables)
    {
        if (is_array($tables)) {
            foreach ($tables as $table) {
                $this->generate($table);
            }
        } else {
            $this->generate($tables);
        }
    }

    protected function generate($table)
    {
        $migrationName = date('Y_m_d_His') . '_create_'. $table .'_table';
        $fields = DB::select("select *
        from
        (SELECT column_name as name, lower(is_nullable) as nullable, data_type, character_maximum_length FROM information_schema.columns
        WHERE table_name= '{$table}') as A
        left join
        (SELECT tc.constraint_type, tc.constraint_name, kcu.column_name, ccu.table_name AS foreign_table_name, ccu.column_name AS foreign_column_name FROM information_schema.table_constraints AS tc JOIN information_schema.key_column_usage AS kcu ON tc.constraint_name = kcu.constraint_name JOIN information_schema.constraint_column_usage AS ccu ON ccu.constraint_name = tc.constraint_name
        WHERE tc.table_name =  '{$table}') as B
        on A.name = B.column_name;");

        $schema = '';
        foreach ($fields as $field) {
            $schema .= $this->generateField($field);
        }
        $schemaOut = $this->replaceFieldsWith($schema, file_get_contents($this->getTemplate('schema.txt')), $table);
        $this->createMigration($table, $schemaOut);
    }

    protected function createMigration($table, $schemaOut)
    {
        $allConfig =  [
            'CLASS' => "Create".Str::studly($table)."Table",
            'UP'    => $schemaOut,
            'DOWN'  => "Schema::dropIfExists('$table');"
        ];

        $migrationOut = $this->replaceMigrationsWith($allConfig, file_get_contents($this->getTemplate('migration.txt')));
    }

    protected function generateField($field)
    {
        if ($field->constraint_type == "PRIMARY KEY") {
            if($field->data_type == "integer"||$field->data_type == "bigint") {
                return "\t\t\t\$table->bigIncrements('$field->name');\n";
            } else {
                return "\t\t\t\$table->string('$field->name');\n";
            }
        }
        if ($field->name == "remember_token") {
            return "\t\t\t\$table->rememberToken();\n";
        }
        if ($field->name == "created_at") {
            return;
        }
        if ($field->name == "updated_at") {
            return "\t\t\t\$table->timestamps();\n";
        }
        if ($field->name == "deleted_at") {
            return "\t\t\t\$table->softDeletes();";
        }

        $length = '';
        if ($field->character_maximum_length) {
            $length = ", $field->character_maximum_length";
        }
        $nullable = '';
        if ($field->nullable == "yes") {
            $nullable = "->nullable()";
        }

        $unique = '';
        if ($field->constraint_type == "UNIQUE") {
            $unique = "->unique()";
        }

        if ($field->data_type == "timestamp without time zone") {
            return "\t\t\t\$table->timestamp('$field->name'$length)$unique$nullable;\n";
        }
        if ($field->data_type == "integer") {
            return "\t\t\t\$table->integer('$field->name'$length)$unique$nullable;\n";
        }
        return "\t\t\t\$table->string('$field->name'$length)$unique$nullable;\n";
    }

    protected function getTemplate($file)
    {
        return __DIR__.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$file;
    }

    protected function removeExcludedTables(array $tables)
    {
        $excludes = ["migrations", "oauth_auth_codes", "oauth_access_tokens", "oauth_refresh_tokens", "oauth_clients", "oauth_personal_access_clients", "password_resets"];
        return array_diff($tables, $excludes);
    }

    protected function replaceFieldsWith($schema, $template, $table)
    {
        $output = str_replace('$FIELDS$', $schema, $template);
        $output = str_replace('$TABLE$', $table, $output);
        return $output;
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
