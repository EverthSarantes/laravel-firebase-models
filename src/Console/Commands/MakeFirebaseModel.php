<?php

namespace Firebase\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeFirebaseModel extends Command
{
    protected $signature = 'make:firebaseModel {name}';
    protected $description = 'Create a new Firebase model';

    public function handle()
    {
        $name = $this->argument('name');
        $modelPath = app_path('Models/Firebase/' . $name . '.php');

        // Verifica si el archivo ya existe
        if (File::exists($modelPath)) {
            $this->error('Model already exists!');
            return 1;
        }

        // Crea el contenido del archivo
        $modelTemplate = <<<PHP
        <?php

        namespace App\Models\Firebase;

        use Firebase\Models\FirebaseModel;

        class $name extends FirebaseModel
        {
            protected \$collection = '$name';
        }
        PHP;

        // Crea el directorio si no existe
        File::ensureDirectoryExists(app_path('Models/Firebase'));

        // Guarda el archivo
        File::put($modelPath, $modelTemplate);

        $this->info('Firebase model created successfully.');
        return 0;
    }
}