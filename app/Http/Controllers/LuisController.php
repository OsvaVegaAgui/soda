<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Desayuno;

use App\Models\Almuerzo;

use App\Models\Refrigerio;

class LuisController extends Controller
{

    public function resolver(Request $request, string $accion, ?string $id = null)
    {
        if ($id !== null) {
            $id = strtolower($id) === 'null' ? null : (ctype_digit($id) ? (int) $id : $id);
        }

        switch ($accion) {
            case 'crear':
            case 'ver':
                return $this->crear();

            default:
                abort(404, 'Página no encontrada.');
        }
    }

  
    protected function crear()
    {
        $desayunos   = Desayuno::all()->keyBy('dia');
        $almuerzos   = Almuerzo::all()->keyBy('dia');
        $refrigerios = Refrigerio::all()->keyBy('dia');

        $orden = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

        $semana = [];
        foreach ($orden as $dia) {
            $semana[$dia] = [
                'desayuno'   => $desayunos[$dia]->platillo   ?? '—',
                'almuerzo'   => $almuerzos[$dia]->platillo   ?? '—',
                'refrigerio' => $refrigerios[$dia]->platillo ?? '—',
            ];
        }

        $mapaEn = [
            'Monday'    => 'Lunes',
            'Tuesday'   => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday'  => 'Jueves',
            'Friday'    => 'Viernes',
        ];
        $hoy = $mapaEn[now()->format('l')] ?? null;

        $meses = [
            1=>'enero', 2=>'febrero', 3=>'marzo', 4=>'abril',
            5=>'mayo', 6=>'junio', 7=>'julio', 8=>'agosto',
            9=>'septiembre', 10=>'octubre', 11=>'noviembre', 12=>'diciembre',
        ];

        $lunes   = now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $viernes = $lunes->copy()->addDays(4);

        $fechaSemana = 'Del ' . $lunes->day . ' al ' . $viernes->day
            . ' de ' . $meses[(int) $viernes->format('n')]
            . ' de ' . $viernes->year;

        return view('pages.menu_site.menuSemanal', compact('semana', 'hoy', 'fechaSemana'));
    }


}