<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Desayuno;
use App\Models\Almuerzo;
use App\Models\Refrigerio;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

        foreach ($dias as $dia) {
            Desayuno::updateOrCreate(['dia' => $dia], ['platillo' => 'Por definir']);
            Almuerzo::updateOrCreate(['dia' => $dia], ['platillo' => 'Por definir']);
            Refrigerio::updateOrCreate(['dia' => $dia], ['platillo' => 'Por definir']);
        }
    }
}
