<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use App\Models\CategoriaTicket;
use App\Models\categoria_instituto;
use Illuminate\Support\Facades\Validator;

class MarcosController extends Controller
{

    public function resolver(Request $request, string $accion, ?string $id = null)
    {
        if ($id !== null) {
            $id = strtolower($id) === 'null' ? null : (ctype_digit($id) ? (int) $id : $id);
        }

        switch ($accion) {
            case 'crear':
                return $this->crear();
            case 'lista':
                return $this->lista();
            case 'editar_view':
                return $this->editar_view($id);
            case 'editar':
                return $this->editar($request, $id);
            case 'eliminar':
                return $this->eliminar($id);
            case 'insertar':
                return $this->insertar($request);
        }
    }

    protected function crear()
    {
        $config = CategoriaTicket::all();
        $categoriaInstituto = categoria_instituto::all();
        return view('pages.ticketes.crear', ['config' => $config], ['categoriaInstituto' => $categoriaInstituto]);
    }

protected function insertar(Request $request)
{
        $validator = Validator::make($request->all(), [
                'nombre' => ['required','string','max:100'],
                'precio' => ['required','numeric'],
                'codigo' => ['required','string'],
                'cantidad' => ['required','integer'],
        ], [
                'nombre.required' => 'El nombre del ticket es obligatorio.',
                'precio.required' => 'El precio del ticket es obligatorio.',
                'codigo.required' => 'El código del ticket es obligatorio.',
                'cantidad.required' => 'La cantidad del ticket es obligatoria.',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

    try {
        $id = DB::table('ticket')->insertGetId([
            'nombre' => $request->input('nombre'),
            'precio' => $request->input('precio'),
            'codigo' => $request->input('codigo'),
            'categoria_id' => $request->input('categoria_id'),
            'cantidad' => $request->input('cantidad'),
            'categoria_instituto_id' => $request->input('categoriaInst'),
        ]);

        return response()->json(['message' => 'Ticket creado exitosamente'], 201);

    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Error al crear el ticket.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
    protected function lista()
    {
        $tickets = Ticket::all();
        $categoriaInstituto = categoria_instituto::all();
        $cateticket = CategoriaTicket::all();
        return view('pages.ticketes.lista', ['tickets' => $tickets]);
    }

    protected function editar_view($id)
    {
        $ticket = Ticket::findOrFail($id);
        $config = CategoriaTicket::all();
        $categoriaInstituto = categoria_instituto::all();
        return view('pages.ticketes.editar', compact('ticket', 'config', 'categoriaInstituto'));
    }

    protected function editar(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'nombre' => ['required','string','max:100'],
            'precio' => ['required','numeric'],
            'codigo' => ['required','string'],
            'cantidad' => ['required','integer'],
        ], [
            'nombre.required' => 'El nombre del ticket es obligatorio.',
            'precio.required' => 'El precio del ticket es obligatorio.',
            'codigo.required' => 'El código del ticket es obligatorio.',
            'cantidad.required' => 'La cantidad del ticket es obligatoria.',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $ticket = Ticket::findOrFail($id);
            $categoriaInstituto = categoria_instituto::all();
            $categoriatipo = CategoriaTicket::all();

            $ticket->nombre = $request->input('nombre');
            $ticket->precio = $request->input('precio');
            $ticket->codigo = $request->input('codigo');
            $ticket->categoria_id = $request->input('categoria_id');
            $ticket->cantidad = $request->input('cantidad');
            $ticket->categoria_instituto_id = $request->input('categoriaInst');

            $ticket->save();

            return response()->json([
                'ok'       => true,
                'id'       => $ticket->id_ticket,
                'message'  => 'Ticket actualizado correctamente.',
                'redirect' => route('ticketes-soda', ['accion' => 'lista']),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al actualizar el ticket en la base de datos.',
                'errors'  => ['server' => [$e->getMessage()]],
            ], 500);
        }
    }

    protected function eliminar($id)
    {

        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->delete();

            return response()->json([
                'ok'      => true,
                'id'      => $id,
                'message' => 'Ticket eliminado correctamente.',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'ok'      => false,
                'message' => 'El ticket indicado no existe.',
            ], 404);

        } catch (\Throwable $e) {
            return response()->json([
                'ok'      => false,
                'message' => 'Error al eliminar el ticket en la base de datos.',
                'errors'  => ['server' => [$e->getMessage()]],
            ], 500);
        }
    }
}
