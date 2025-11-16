<?php

namespace Database\Seeders;

use App\Models\Abonnement;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ajout d’abonnement
        $abonnement = new Abonnement();
        $abonnement->id = Str::uuid();
        $abonnement->type_abonnement = 'debutant';
        $abonnement->save();
        $this->command->info("     - Abonnement par defaut créé");
    }
}
