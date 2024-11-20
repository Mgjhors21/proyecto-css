<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(3)->create();

        $rol = new Rol();
        $rol->id=1;
        $rol->rol="adminitrador";
        $rol->save();

        $rol2 = new Rol();
        $rol2->id=2;
        $rol2->rol="coordinador";
        $rol2->save();

        $rol3 = new Rol();
        $rol3->id=3;
        $rol3->rol="secretaria";
        $rol3->save();

        $rol4 = new Rol();
        $rol4->id=4;
        $rol4->rol="estudiante";
        $rol4->save();



        \App\Models\User::factory()->create([
            'id' => '1',
            'name' => 'Admin',
            'last_name' => 'principal',
            'phone' => '0000000000',
            'email' => 'admin@uniautonoma.edu.co',
            'password' => 'AdminPassword#',
            'user_type' => '1',
        ]);



        \App\Models\User::factory()->create([
            'id' => '3',
            'name' => 'admin',
            'last_name' => 'admin',
            'phone' => '00000',
            'email' => 'admin@uniautonoma.edu.co',
            'password' => 'Admin2024#',
            'user_type' => '1',

        ]);

        \App\Models\User::factory()->create([
            'id' => '4',
            'name' => 'Zulema',
            'last_name' => 'Leon Escobar',
            'phone' => '00000',
            'email' => 'coordinacion.software@uniautonoma.edu.co',
            'password' => 'Coordinador2024#',
            'user_type' => '2',

        ]);


        \App\Models\User::factory()->create([
            'id' => '21',
            'name' => 'Nayda',
            'last_name' => 'Ordóñez Torres',
            'phone' => '', // No hay registro de teléfono
            'email' => 'coordinacion.degri@uniautonoma.edu.co',
            'password' => 'Coordinador2024#',
            'user_type' => '2',
        ]);


        \App\Models\User::factory()->create([
            'id' => '27',
            'name' => 'Nayda',
            'last_name' => 'Ordóñez Torres',
            'phone' => '', // No hay registro de teléfono
            'email' => 'coordinacion.relaciones@uniautonoma.edu.co',
            'password' => 'Coordinador2024#',
            'user_type' => '2',
        ]);


     //$this->call(RolesSeeder::class)

    }
}
