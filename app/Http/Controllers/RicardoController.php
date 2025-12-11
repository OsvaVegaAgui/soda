<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RicardoController extends Controller
{

    public function resolver(Request $request, string $accion, ?string $id = null)
    {
        if ($id !== null) {
            $id = strtolower($id) === 'null' ? null : (ctype_digit($id) ? (int) $id : $id);
        }

        switch ($accion) {
            case 'crear':
                return $this->crear();
        }
    }

    public function insertar($user_id, $tabla, $registro_id, $accion, $valores_antes, $valores_despues)
    {
        DB::table('auditoria')->insert([
            'user_id' => $user_id,
            'tabla' => $tabla,
            'registro_id' => $registro_id,
            'accion' => $accion,
            'valores_antes' => $valores_antes,
            'valores_despues' => $valores_despues
            // 'created_at' => now(),
            // 'updated_at' => now(),
        ]);
    }


}
