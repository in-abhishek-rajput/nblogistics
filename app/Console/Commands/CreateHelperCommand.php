<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateHelperCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:helper {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new Helper file in App/Helpers.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $helperName = $this->argument('name');
        $directoryPath = app_path('Helpers');
        $filePath = "{$directoryPath}/{$helperName}.php";

        // 1. Ensure the directory exists
        // The ensureDirectoryExists method creates the directory recursively if it doesn't exist.
        File::ensureDirectoryExists($directoryPath);

        // 2. Check if the file already exists
        if (File::exists($filePath)) {
            $this->error("Helper file '{$helperName}.php' already exists!");
            return Command::FAILURE;
        }

        // 3. Define the file content (stub)
        $stub = <<<EOD
<?php

namespace App\Helpers;

class {$helperName}
{
    public function __construct()
    {
    }

}
EOD;

        // 4. Create the file
        File::put($filePath, $stub);

        $this->info("Repository file '{$helperName}.php' created successfully in App/Repositories/ directory.");
        return Command::SUCCESS;
    }
}
