<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeLayer extends Command
{
    protected $signature = 'make:layer {name : The name of the module (e.g. Product)}';
    protected $description = 'Create Service, Repository, Interface, DTO and bind them automatically';

    public function handle()
    {
        $name = $this->argument('name');

        // 1. Buat File-file Arsitektur
        $files = [
            'DTO' => [
                'path' => app_path("DTOs/{$name}DTO.php"),
                'content' => $this->getDtoStub($name)
            ],
            'Interface' => [
                'path' => app_path("Interfaces/{$name}RepositoryInterface.php"),
                'content' => $this->getInterfaceStub($name)
            ],
            'Repository' => [
                'path' => app_path("Repositories/Eloquent{$name}Repository.php"),
                'content' => $this->getRepositoryStub($name)
            ],
            'Service' => [
                'path' => app_path("Services/{$name}Service.php"),
                'content' => $this->getServiceStub($name)
            ],
        ];

        foreach ($files as $type => $file) {
            $this->createFile($file['path'], $file['content'], $type);
        }

        // 2. Update Service Provider Otomatis
        $this->updateServiceProvider($name);

        $this->info("Layer structure for {$name} created and registered successfully!");
    }

    protected function createFile($path, $content, $type)
    {
        $directory = dirname($path);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::exists($path)) {
            $this->warn("File {$type} already exists inside {$path}. Skipping...");
            return;
        }

        File::put($path, $content);
        $this->info("Created {$type}: {$path}");
    }

    protected function updateServiceProvider($name)
    {
        $providerPath = app_path('Providers/RepositoryServiceProvider.php');

        if (!File::exists($providerPath)) {
            $this->error("RepositoryServiceProvider not found! Did you create it?");
            return;
        }

        $content = File::get($providerPath);
        $hook = '// #BINDING-HOOK#';

        $searchString = "{$name}RepositoryInterface::class";
        if (str_contains($content, $searchString)) {
            $this->warn("Binding for {$name} seems to already exist in Provider.");
            return;
        }

        $bindingCode = "\$this->app->bind(\n" .
            "            \\App\\Interfaces\\{$name}RepositoryInterface::class,\n" .
            "            \\App\\Repositories\\Eloquent{$name}Repository::class\n" .
            "        );" . PHP_EOL . PHP_EOL . "        " . $hook;

        if (str_contains($content, $hook)) {
            $newContent = str_replace($hook, $bindingCode, $content);
            File::put($providerPath, $newContent);
            $this->info("Updated RepositoryServiceProvider binding.");
        } else {
            $this->error("Hook '{$hook}' not found in RepositoryServiceProvider.");
        }
    }

    // --- TEMPLATES / STUBS ---

    protected function getDtoStub($name)
    {
        return <<<EOT
<?php

namespace App\DTOs;

readonly class {$name}DTO
{
    public function __construct(
        // public string \$param
    ) {}

    public static function fromRequest(\$request): self
    {
        return new self(
            // param: \$request->validated('param')
        );
    }
}
EOT;
    }

    protected function getInterfaceStub($name)
    {
        return <<<EOT
<?php

namespace App\Interfaces;

interface {$name}RepositoryInterface
{
    public function getAll();
    public function findById(\$id);
    public function create(array \$data);
    public function update(\$id, array \$data);
    public function delete(\$id);
}
EOT;
    }

    protected function getRepositoryStub($name)
    {
        return <<<EOT
<?php

namespace App\Repositories;

use App\Interfaces\\{$name}RepositoryInterface;
use App\Models\\{$name};

class Eloquent{$name}Repository implements {$name}RepositoryInterface
{
    public function getAll()
    {
        return {$name}::all();
    }

    public function findById(\$id)
    {
        return {$name}::findOrFail(\$id);
    }

    public function create(array \$data)
    {
        return {$name}::create(\$data);
    }

    public function update(\$id, array \$data)
    {
        return {$name}::where('id', \$id)->update(\$data);
    }

    public function delete(\$id)
    {
        return {$name}::destroy(\$id);
    }
}
EOT;
    }

    protected function getServiceStub($name)
    {
        return <<<EOT
<?php

namespace App\Services;

use App\DTOs\\{$name}DTO;
use App\Interfaces\\{$name}RepositoryInterface;

class {$name}Service
{
    public function __construct(
        protected {$name}RepositoryInterface \$repository
    ) {}

    // public function process({$name}DTO \$dto) {}
}
EOT;
    }
}
