<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\Pais;


class RouterController extends Controller
{
    public function resolver(Request $request, string $accion, ?string $id = null)
    {
        if ($id !== null) {
            $id = strtolower($id) === 'null' ? null : (ctype_digit($id) ? (int) $id : $id);
        }

        switch ($accion) {

            case 'crear':
                return $this->paisesCrearForm();

            case 'insertar':
                return $this->paisesCrearPost($request);

            case 'lista':
                return $this->paisesLista();

            case 'ver-editar':
                return $this->paisesEditarForm($id);

            case 'editar':
                return $this->paisesEditarPost($request, $id);
            
            case 'eliminar':
                return $this->paisesEliminar($id);


            default:
                abort(404, 'Acción no soportada para paises.');
        }
    }

    protected function paisesCrearForm()
    {
        return view('pages.paises.crear');
    }

    protected function paisesCrearPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'txtName'       => ['required','string','max:20'],
            'txtExtension'  => ['required','numeric','min:0'],
            'txtFecha'      => ['required','date'],
            'txtHabitantes' => ['required','integer','min:0'],
        ], [
            'txtName.required'       => 'El nombre del país es obligatorio.',
            'txtName.max'            => 'El nombre no puede tener más de 20 caracteres.',
            'txtExtension.required'  => 'La extensión es obligatoria.',
            'txtExtension.numeric'   => 'La extensión debe ser un número.',
            'txtFecha.required'      => 'La fecha de independencia es obligatoria.',
            'txtHabitantes.required' => 'La cantidad de habitantes es obligatoria.',
            'txtHabitantes.integer'  => 'La cantidad de habitantes debe ser un número entero.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Revise los campos del formulario.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $id = DB::table('paises')->insertGetId([
                'nombre'              => $request->input('txtName'),
                'extension'           => $request->input('txtExtension'),
                'fecha_independencia' => $request->input('txtFecha'),
                'habitantes'          => $request->input('txtHabitantes'),
            ]);

            return response()->json([
                'ok'      => true,
                'id'      => $id,
                'message' => 'País creado correctamente.',
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al guardar el país en la base de datos.',
                'errors'  => ['server' => [$e->getMessage()]],
            ], 500);
        }
    }

    protected function paisesLista()
    {
        $paises = Pais::all();
        return view('pages.paises.lista', compact('paises'));
    }

    protected function paisesEditarForm($id)
    {
        $pais = Pais::findOrFail($id);
        return view('pages.paises.editar', compact('pais'));
    }

    protected function paisesEditarPost(Request $request, $id)
    {
        // $validator = Validator::make($request->all(), [
        //     'txtName'       => ['required','string','max:20'],
        //     'txtExtension'  => ['required','numeric','min:0'],
        //     'txtFecha'      => ['required','date'],
        //     'txtHabitantes' => ['required','integer','min:0'],
        // ], [
        //     'txtName.required'       => 'El nombre del país es obligatorio.',
        //     'txtName.max'            => 'El nombre no puede tener más de 20 caracteres.',
        //     'txtExtension.required'  => 'La extensión es obligatoria.',
        //     'txtExtension.numeric'   => 'La extensión debe ser un número.',
        //     'txtFecha.required'      => 'La fecha de independencia es obligatoria.',
        //     'txtHabitantes.required' => 'La cantidad de habitantes es obligatoria.',
        //     'txtHabitantes.integer'  => 'La cantidad de habitantes debe ser un número entero.',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'message' => 'Revise los campos del formulario.',
        //         'errors'  => $validator->errors(),
        //     ], 422);
        // }

        try {
            $pais = Pais::findOrFail($id);

            $pais->nombre              = $request->input('txtName');
            $pais->extension           = $request->input('txtExtension');
            $pais->fecha_independencia = $request->input('txtFecha');
            $pais->habitantes          = $request->input('txtHabitantes');
            $pais->save();

            return response()->json([
                'ok'       => true,
                'id'       => $pais->idPais,
                'message'  => 'País actualizado correctamente.',
                'redirect' => route('paises.procesar', ['accion' => 'lista']),
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al actualizar el país en la base de datos.',
                'errors'  => ['server' => [$e->getMessage()]],
            ], 500);
        }
    }

    protected function paisesEliminar($id)
    {
        try {
            $pais = Pais::findOrFail($id);
            $pais->delete();

            return response()->json([
                'ok'      => true,
                'id'      => $id,
                'message' => 'País eliminado correctamente.',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'ok'      => false,
                'message' => 'El país indicado no existe.',
            ], 404);

        } catch (\Throwable $e) {
            return response()->json([
                'ok'      => false,
                'message' => 'Error al eliminar el país en la base de datos.',
                'errors'  => ['server' => [$e->getMessage()]],
            ], 500);
        }
    }



    
}
