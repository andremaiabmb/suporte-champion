<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SectorSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Secretaria Acadêmica', 'email' => null],
            ['name' => 'Financeiro',           'email' => null],
            ['name' => 'Tecnologia da Informação', 'email' => null],
        ] as $row) {
            Sector::firstOrCreate(['slug' => Str::slug($row['name'])], [
                'name' => $row['name'],
                'email' => $row['email'],
                'is_active' => true,
            ]);
        }
    }
}
