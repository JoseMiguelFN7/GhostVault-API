<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Secret;

class CleanExpiredSecrets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secrets:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina permanentemente los secretos que han expirado';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Count expired secrets
        $count = Secret::where('expires_at', '<', now())->count();

        if ($count > 0) {
            // Delete expired secrets
            Secret::where('expires_at', '<', now())->delete();
            
            // Output the number of deleted secrets
            $this->info("Se han eliminado {$count} secretos expirados.");
        } else {
            $this->comment('No hay secretos expirados para limpiar.');
        }
    }
}
