<?php

namespace Database\Seeders;

use App\Models\Abonnement;
use App\Models\Admin;
use App\Models\Commune;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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

        //Ajout de communes
        $communes = [
            'Abobo',
            'Adjamé',
            'Attécoubé',
            'Cocody',
            'Koumassi',
            'Marcory',
            'Plateau',
            'Port-Bouët',
            'Treichville',
            'Yopougon',
            'Songon',
            'Bingerville',
            'Anyama'
        ];

        foreach ($communes as $localite) {
            $commune = new Commune();
            $commune->id = (string) Str::uuid();
            $commune->localite = $localite;
            $commune->save();
        }
        $this->command->info("     - Quelques communes crees");

        //Ajout du super admin
        $admin = new Admin();
        $admin->id = (string) Str::uuid();
        $admin->nom_admin = 'Administrateur';
        $admin->email_admin = 'administrateur@gmail.com';
        $admin->tel_admin = '0102030405';
        $admin->password_admin = Hash::make('admin123');
        $admin->role = 2;
        $admin->save();
        $this->command->info("     - Super Admin créé");
    }
}
