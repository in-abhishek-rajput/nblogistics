<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateRepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new Repository file in App/Repositories.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $repositoryName = $this->argument('name');
        $directoryPath = app_path('Repositories');
        $filePath = "{$directoryPath}/{$repositoryName}.php";

        // 1. Ensure the directory exists
        // The ensureDirectoryExists method creates the directory recursively if it doesn't exist.
        File::ensureDirectoryExists($directoryPath);

        // 2. Check if the file already exists
        if (File::exists($filePath)) {
            $this->error("Repository file '{$repositoryName}.php' already exists!");
            return Command::FAILURE;
        }

        // 3. Define the file content (stub)
        $stub = <<<EOD
<?php

namespace App\Repositories;

class {$repositoryName}
{
    // Implement repository methods here
    public \$model;

    public function __construct([MODEL_CLASS] \$model)
    {
        \$this->model = \$model;
    }

    // Get data by id
    public function findByID(\$id)
    {
        return \$this->model->findorFail(\$id);
    }

    // Create new recoard
    public function create(\$params)
    {
        \$data = \$this->model->create(\$params);
        return \$data;
    }

    // Update recoard
    public function update(\$params, \$id)
    {
        \$data = \$this->findByID(\$id)->update(\$params);
        return \$data;
    }

    //Filter data
    public function filter(\$params)
    {

    }
}
EOD;

        // 4. Create the file
        File::put($filePath, $stub);

        $this->info("Repository file '{$repositoryName}.php' created successfully in App/Repositories/ directory.");
        return Command::SUCCESS;
    }
}
