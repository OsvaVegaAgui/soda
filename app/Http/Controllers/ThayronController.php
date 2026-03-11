<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\ProductoSoda;
use App\Http\Controllers\RicardoController;

class ThayronController extends Controller
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
                return $this->editar($id);
            case 'editar':
                return $this->productosEditarPost($request, $id);
            case 'eliminar':
                return $this->eliminar($id);
            default:
                abort(404, 'Acción no soportada.');
        }
    }

    protected function crear()
    {
        return view('pages.productos_soda.crear');
    }

    protected function insertar(Request $request)
    {
        $validated = $request->validate([
            'nombre'          => ['required', 'string', 'max:100'],
            'precio'          => ['required', 'numeric', 'min:0'],
            'codigo_softland' => ['nullable', 'string', 'max:50'],
            'codigo_barras'   => ['nullable', 'string', 'max:50'],
            'activo'          => ['required', 'in:0,1'],
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.max'      => 'El nombre no puede tener más de 100 caracteres.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric'  => 'El precio debe ser un número válido.',
            'activo.required' => 'Debe seleccionar el estado del producto.',
        ]);

        $producto = ProductoSoda::create($validated);

        try {
            (new RicardoController)->insertar(
                auth()->id() ?? 1,
                'productos_soda',
                $producto->id_producto_soda,
                'create',
                'NA',
                json_encode($validated)
            );
        } catch (\Throwable) {
            // La auditoría no debe bloquear la operación principal
        }

        if ($request->ajax()) {
            return response()->json([
                'ok'      => true,
                'message' => 'Producto creado correctamente.',
                'redirect' => route('productos-soda', ['accion' => 'lista']),
            ]);
        }

        return redirect()->route('productos-soda', ['accion' => 'lista'])
            ->with('success', 'Producto creado correctamente.');
    }

    protected function lista()
    {
        $productos = ProductoSoda::orderBy('id_producto_soda', 'asc')->get();
        return view('pages.productos_soda.lista', compact('productos'));
    }

    protected function editar($id)
    {
        $soda = ProductoSoda::findOrFail($id);
        return view('pages.productos_soda.editar', compact('soda'));
    }

    protected function productosEditarPost(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre'          => ['required', 'string', 'max:100'],
            'precio'          => ['required', 'numeric', 'min:0'],
            'codigo_softland' => ['nullable', 'string', 'max:50'],
            'codigo_barras'   => ['nullable', 'string', 'max:50'],
            'activo'          => ['required', 'in:0,1'],
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.max'      => 'El nombre no puede tener más de 100 caracteres.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric'  => 'El precio debe ser un número válido.',
            'activo.required' => 'Debe seleccionar el estado del producto.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Revise los campos del formulario.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $soda = ProductoSoda::findOrFail($id);
            $soda->fill($request->only(['nombre', 'codigo_softland', 'codigo_barras', 'precio', 'activo']));
            $soda->save();

            return response()->json([
                'ok'      => true,
                'id'      => $soda->id_producto_soda,
                'message' => 'Producto actualizado correctamente.',
                'redirect' => route('productos-soda', ['accion' => 'lista']),
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al actualizar el producto.',
                'errors'  => ['server' => [$e->getMessage()]],
            ], 500);
        }
    }

    protected function eliminar($id)
    {
        try {
            $soda = ProductoSoda::findOrFail($id);
            $soda->delete();

            return response()->json([
                'ok'      => true,
                'message' => 'Producto eliminado correctamente.',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'ok'      => false,
                'message' => 'El producto indicado no existe.',
            ], 404);

        } catch (\Throwable $e) {
            return response()->json([
                'ok'      => false,
                'message' => 'Error al eliminar el producto.',
                'errors'  => ['server' => [$e->getMessage()]],
            ], 500);
        }
    }
}
