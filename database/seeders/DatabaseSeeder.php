<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Provider;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
      

        // // 3. Usuários
        // User::create([
        //     'name' => 'Admin Sesi',
        //     'email' => 'admin@sesi.com',
        //     'password' => bcrypt('12345678'),
        //     'role' => 'admin'
        // ]);

        // User::create([
        //     'name' => 'Gerente Transportadora',
        //     'email' => 'cliente@empresa.com',
        //     'password' => bcrypt('12345678'),
        //     'role' => 'company',
        //     'company_id' => $empresa->id
        // ]);

        // 4. Um agendamento de teste
        // \App\Models\Appointment::create([
        //     'company_id' => $empresa->id,
        //     'provider_id' => $clinica->id,
        //     'exam_type' => 'ASO Admissional',
        //     'scheduled_at' => now()->addDays(2)
        // ]);

        Role::create(['name' => 'Motorista de Caminhão', 'cbo' => '7825-10']);
        Role::create(['name' => 'Auxiliar Administrativo', 'cbo' => '4110-05']);
        Role::create(['name' => 'Soldador', 'cbo' => '7243-15']);
        Role::create(['name' => 'Eletricista', 'cbo' => '7156-15']);
    }
}
