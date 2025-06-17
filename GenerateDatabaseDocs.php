<?php
/**
 * GenerateDatabaseDocs
 *
 * This command generates a complete Markdown documentation file for the configured MySQL database schema.
 * The output includes:
 * - List of all tables and their columns
 * - Associated Eloquent model (if available)
 * - Example JSON data from each table (real or mocked)
 * - Identification of default Laravel tables (e.g. `migrations`, `jobs`, etc.)
 * - Custom tables are listed first, Laravel's default ones last
 * - Extracts `$fillable`, `$casts`, and `$hidden` attributes from models
 * - Detects and lists model relationships (`hasOne`, `hasMany`, `belongsTo`, etc.)
 *
 * Ideal for quick reference or as a context file for AI tools like GitHub Copilot or ChatGPT.
 * Author: @malkafly the guy from @darkinquisition
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;

class GenerateDatabaseDocs extends Command
{
    protected $signature = 'docs:database-docs';

    protected $description = 'Generate database schema documentation with examples in Markdown for AI context';

    private $laravelTables = [
        'migrations',
        'failed_jobs',
        'password_reset_tokens',
        'personal_access_tokens',
        'jobs',
        'job_batches',
        'notifications',
        'cache',
        'sessions'
    ];

    public function handle()
    {
        $database = config('database.connections.mysql.database');
        $this->info("Generating documentation for database: {$database}");

        $columns = DB::table('INFORMATION_SCHEMA.COLUMNS')
            ->select(
                'TABLE_NAME as table',
                'COLUMN_NAME as column',
                'COLUMN_TYPE as type',
                'IS_NULLABLE as nullable',
                'COLUMN_KEY as key',
                'COLUMN_DEFAULT as default_value',
                'EXTRA as extra',
                'COLUMN_COMMENT as column_comment',
            )
            ->where('TABLE_SCHEMA', $database)
            ->orderBy('TABLE_NAME')
            ->orderBy('ORDINAL_POSITION')
            ->get();

        if ($columns->isEmpty()) {
            $this->error("No tables found in database {$database}.");
            return 1;
        }

        $this->info("Found " . $columns->count() . " columns in total.");

        $tables = [];

        foreach ($columns as $column) {
            $tables[$column->table][] = $column;
        }

        $this->info("Organizing " . count($tables) . " tables...");

        uksort($tables, function ($a, $b) {
            $aIsLaravel = in_array($a, $this->laravelTables);
            $bIsLaravel = in_array($b, $this->laravelTables);
            return $aIsLaravel <=> $bIsLaravel ?: strcmp($a, $b);
        });

        $markdown = "# Database Documentation: `{$database}`\n\n";
        $markdown .= "This document was automatically generated to provide an overview of the MySQL database schema used in this Laravel project.\n";
        $markdown .= "It is optimized to support AI-assisted development by summarizing tables, columns, associated Eloquent models,\n";
        $markdown .= "fillable attributes, casts, hidden fields, and model relationships. Each section includes an example JSON payload\n";
        $markdown .= "(retrieved or mocked) to give context for the structure of the data.\n";
        $markdown .= "\n";

        $tableCount = 0;
        foreach ($tables as $tableName => $tableColumns) {
            $tableCount++;
            $this->info("Processing table {$tableCount} of " . count($tables) . ": {$tableName}");
            
            $isLaravelTable = in_array($tableName, $this->laravelTables);
            $laravelTag = $isLaravelTable ? " *(Laravel Default)*" : "";

            $markdown .= "## Table: `{$tableName}`{$laravelTag}\n\n";

            $modelClass = $this->findModelForTable($tableName);
            if ($modelClass) {
                try {
                    $model = new $modelClass();
                    $markdown .= "**Associated Model:** `{$modelClass}`\n\n";

                    $reflection = new ReflectionClass($model);
                    $props = ['fillable', 'casts', 'hidden'];
                    foreach ($props as $prop) {
                        if ($reflection->hasProperty($prop)) {
                            $reflectionProperty = $reflection->getProperty($prop);
                            $reflectionProperty->setAccessible(true);
                            $value = $reflectionProperty->getValue($model);
                            if (!empty($value)) {
                                $markdown .= "**" . ucfirst($prop) . ":** `" . str_replace('`', '\`', json_encode($value)) . "`\n\n";
                            }
                        }
                    }

                    $markdown .= "**Relations:**\n";
                    $hasRelations = false;
                    foreach (get_class_methods($model) as $method) {
                        if (in_array($method, ['getRelations', 'getRelation']) || strpos($method, '__') === 0) {
                            continue; 
                        }
                        
                        try {
                            $reflection = new ReflectionMethod($model, $method);
                            if ($reflection->getNumberOfParameters() > 0 || $reflection->isStatic()) {
                                continue;
                            }
                            
                            $result = $model->$method();
                            if ($result instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                                $related = get_class($result->getRelated());
                                $markdown .= "- `{$method}()` → `" . class_basename($result) . "(`{$related}`)`\n";
                                $hasRelations = true;
                            }
                        } catch (\Throwable $e) {
                            // skip
                        }
                    }
                    
                    if (!$hasRelations) {
                        $markdown .= "- No relations found\n";
                    }
                    $markdown .= "\n";
                } catch (\Throwable $e) {
                    $this->warn("Error processing model for table {$tableName}: " . $e->getMessage());
                }
            }

            $markdown .= "| Column | Type | Nullable | Key | Default | Extra | Comment |\n";
            $markdown .= "|--------|------|----------|-----|---------|-------|---------|\n";

            foreach ($tableColumns as $col) {
                $default = $col->default_value ?? 'NULL';
                $comment = $col->column_comment ?? '';
                $markdown .= "| `{$col->column}` | `{$col->type}` | `{$col->nullable}` | `{$col->key}` | `{$default}` | `{$col->extra}` | `{$comment}` |\n";
            }

            $markdown .= "\n### Example JSON:\n\n";

            try {
                $example = DB::table($tableName)->first();
            } catch (\Exception $e) {
                $this->warn("Error fetching example for table {$tableName}: " . $e->getMessage());
                $example = null;
            }

            if (!$example) {
                $example = [];
                foreach ($tableColumns as $col) {
                    $example[$col->column] = $this->mockValue($col->type);
                }
            } else {
                $example = (array) $example;
                // Ensure binary or complex values are properly formatted
                foreach ($example as $key => $value) {
                    if (is_resource($value) || $value instanceof \stdClass) {
                        $example[$key] = '[Complex Data]';
                    }
                }
            }

            try {
                $jsonString = json_encode($example, JSON_PRETTY_PRINT);
                if ($jsonString === false) {
                    $this->warn("Error encoding JSON for table {$tableName}: " . json_last_error_msg());
                    // Replace with simplified values in case of failure
                    $example = array_map(function ($value) {
                        return is_scalar($value) ? $value : '[Complex Data]';
                    }, $example);
                    $jsonString = json_encode($example, JSON_PRETTY_PRINT);
                }
                $markdown .= "```json\n";
                $markdown .= $jsonString;
                $markdown .= "\n```\n\n";
            } catch (\Throwable $e) {
                $this->warn("Error processing JSON for table {$tableName}: " . $e->getMessage());
                $markdown .= "```json\n{\"error\": \"Data too complex to display\"}\n```\n\n";
            }
        }

        $this->info("Documentation generated. Saving file...");
        $outputPath = base_path("db-documentation-{$database}.md");
        
        try {
            File::put($outputPath, $markdown);
            $this->info("Documentation successfully saved to: {$outputPath}");
            $this->info("File size: " . number_format(strlen($markdown)) . " bytes");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error saving documentation: " . $e->getMessage());
            return 1;
        }
    }

    private function mockValue($type)
    {
        return match (true) {
            str_contains($type, 'int') => 0,
            str_contains($type, 'varchar'), str_contains($type, 'text') => 'example',
            str_contains($type, 'timestamp'), str_contains($type, 'datetime') => '2025-01-01 00:00:00',
            str_contains($type, 'date') => '2025-01-01',
            str_contains($type, 'decimal'), str_contains($type, 'float') => 0.0,
            default => null
        };
    }

    private function findModelForTable(string $table): ?string
    {
        $modelPath = app_path('Models');
        $files = File::files($modelPath);

        foreach ($files as $file) {
            $class = 'App\\Models\\' . $file->getFilenameWithoutExtension();

            if (class_exists($class)) {
                $model = new $class();

                if (method_exists($model, 'getTable') && $model->getTable() === $table) {
                    return $class;
                }
            }
        }

        return null;
    }
}
