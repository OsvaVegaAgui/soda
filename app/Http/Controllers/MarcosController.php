<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Ticket;
use App\Models\CategoriaTicket;

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
            case 'insertar':
                return $this->insertar($request);
            case 'lista':
                return $this->lista();
            case 'ver-editar':
                return $this->verEditar($id);
            case 'editar':
                return $this->editar($request, $id);
            case 'eliminar':
                return $this->eliminar($request, $id);
            default:
                abort(404, 'Acción no soportada.');
        }
    }

    protected function crear()
    {
        $categorias = CategoriaTicket::orderBy('nombre')->get();
        return view('pages.ticketes.crear', compact('categorias'));
    }

    protected function insertar(Request $request)
    {
        $validated = $request->validate([
            'nombre'      => ['required', 'string', 'max:150'],
            'codigo'      => ['required', 'string', 'max:50', Rule::unique('ticket', 'codigo')],
            'categoria_d' => ['required', 'integer', Rule::exists('categoria_tiquetes', 'id_categoria')],
            'precio'      => ['required', 'numeric', 'min:0'],
        ], [
            'nombre.required'      => 'El nombre del tiquete es obligatorio.',
            'nombre.max'           => 'El nombre no puede superar 150 caracteres.',
            'codigo.required'      => 'El código Softland es obligatorio.',
            'codigo.unique'        => 'Ya existe un tiquete con ese código.',
            'categoria_d.required' => 'Debe seleccionar una categoría.',
            'categoria_d.exists'   => 'La categoría seleccionada no es válida.',
            'precio.required'      => 'El precio es obligatorio.',
            'precio.numeric'       => 'El precio debe ser un número.',
        ]);

        $ticket = Ticket::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'ok'      => true,
                'message' => 'Tiquete creado correctamente.',
                'redirect' => route('ticketes-soda', ['accion' => 'lista']),
            ]);
        }

        return redirect()->route('ticketes-soda', ['accion' => 'lista'])
            ->with('success', 'Tiquete creado correctamente.');
    }

    protected function lista()
    {
        $tickets = Ticket::with('categoria')
            ->orderBy('id_ticket', 'asc')
            ->get();

        return view('pages.ticketes.lista', compact('tickets'));
    }

    protected function verEditar($id)
    {
        $ticket     = Ticket::findOrFail($id);
        $categorias = CategoriaTicket::orderBy('nombre')->get();
        return view('pages.ticketes.editar', compact('ticket', 'categorias'));
    }

    protected function editar(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre'      => ['required', 'string', 'max:150'],
            'codigo'      => ['required', 'string', 'max:50', Rule::unique('ticket', 'codigo')->ignore($id, 'id_ticket')],
            'categoria_d' => ['required', 'integer', Rule::exists('categoria_tiquetes', 'id_categoria')],
            'precio'      => ['required', 'numeric', 'min:0'],
        ], [
            'nombre.required'      => 'El nombre del tiquete es obligatorio.',
            'codigo.required'      => 'El código Softland es obligatorio.',
            'codigo.unique'        => 'Ya existe otro tiquete con ese código.',
            'categoria_d.required' => 'Debe seleccionar una categoría.',
            'precio.required'      => 'El precio es obligatorio.',
            'precio.numeric'       => 'El precio debe ser un número.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Revise los campos del formulario.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->fill($request->only(['nombre', 'codigo', 'categoria_d', 'precio']));
            $ticket->save();

            return response()->json([
                'ok'      => true,
                'id'      => $ticket->id_ticket,
                'message' => 'Tiquete actualizado correctamente.',
                'redirect' => route('ticketes-soda', ['accion' => 'lista']),
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al actualizar el tiquete.',
                'errors'  => ['server' => [$e->getMessage()]],
            ], 500);
        }
    }

    protected function eliminar(Request $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->delete();

            return response()->json([
                'ok'      => true,
                'message' => 'Tiquete eliminado correctamente.',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'ok'      => false,
                'message' => 'El tiquete indicado no existe.',
            ], 404);

        } catch (\Throwable $e) {
            return response()->json([
                'ok'      => false,
                'message' => 'Error al eliminar el tiquete.',
                'errors'  => ['server' => [$e->getMessage()]],
            ], 500);
        }
    }
}
