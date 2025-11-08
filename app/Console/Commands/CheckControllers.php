<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CheckControllers extends Command
{
    protected $signature = 'check:controllers';
    protected $description = 'Check all controllers used in routes/web.php and create missing ones';

    public function handle()
    {
        $routesFile = base_path('routes/web.php');
        if (!File::exists($routesFile)) {
            $this->error('routes/web.php not found!');
            return 1;
        }

        $content = File::get($routesFile);

        // Match all controllers: SomethingController::class
        preg_match_all('/([\w\\\\]+Controller)::class/', $content, $matches);

        $controllers = array_unique($matches[1]);

        foreach ($controllers as $controllerFQN) {
            // Convert FQN to path
            $controllerPath = base_path('app/' . str_replace('App\\', '', str_replace('\\', '/', $controllerFQN)) . '.php');

            if (!File::exists($controllerPath)) {
                $this->warn("Controller missing: $controllerFQN");
                $this->createController($controllerPath, $controllerFQN);
            } else {
                $this->info("Controller exists: $controllerFQN");
            }
        }

        $this->info('Controller check complete.');
    }

    private function createController($path, $fqn)
    {
        $namespace = implode('\\', array_slice(explode('\\', $fqn), 0, -1));
        $class = substr($fqn, strrpos($fqn, '\\') + 1);

        $stub = <<<PHP
<?php

namespace $namespace;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class $class extends Controller
{
    // Auto-generated stub
}
PHP;

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $stub);

        $this->info("Created stub controller: $fqn");
    }
}
