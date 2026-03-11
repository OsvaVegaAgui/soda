<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Mail\ResetCodeMail;
use Illuminate\Support\Facades\Validator;




class KeylorController extends Controller
{

        public function resolver(Request $request, string $accion, ?string $id = null)
        {
            if ($id !== null) {
                $id = strtolower($id) === 'null' ? null : (ctype_digit($id) ? (int) $id : $id);
            }

            switch ($accion) {

                case 'crear':
                    return $this->crearUsuario();

                case 'insertar':
                    return $this->usuariosCrearPost($request);

                case 'lista':
                return $this->listaUsuario();

                case 'ver-editar':
                return $this->usuariosEditar($id);

                case 'editar':
                 return $this->usuariosEditarPost($request, $id);
                
                case 'eliminar':
                return $this->usuariosEliminar($id);

                case 'login':
                return $this->login();

                case 'loginConfirmacion':
                return $this->loginConfi($request);

                case 'send-code':
                return $this->sendCode($request);

                case 'validar-token':   
                return $this->validarToken($request);

                case 'cambiar-contrasena':  
                return $this->cambiarContrasena($request);

                case 'restablecer':
                return $this->vistaRestablecer();

                case 'logout':
                return $this->logout($request);

                default:
                    abort(404, 'Acción no soportada para Usuarios.');
            }
        }

        public function crearUsuario()
        {
            return view('pages.usuarios.crear');
        }

        public function vistaRestablecer()
        {
            return view('pages.usuarios.restablecerContraseña');
        }

        public function usuariosCrearPost(Request $request)
    {
        // VALIDACIONES
        $validator = Validator::make($request->all(), [
            'name'     => ['required','string','max:100'],
            'email'    => ['required','email','unique:users,email'],
            'password' => ['required','string','min:4'],
            'rol'      => ['required','integer'],
        ], [
            'name.required'     => 'El nombre es obligatorio.',
            'email.required'    => 'El correo es obligatorio.',
            'email.email'       => 'Debes ingresar un correo válido.',
            'email.unique'      => 'El correo ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Revisa los campos del formulario.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {

            $id = DB::table('users')->insertGetId([
                'name'       => $request->input('name'),
                'rol'        => $request->input('rol'),
                'email'      => $request->input('email'),
                'password'   => Hash::make($request->input('password')),
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'ok'      => true,
                'id'      => $id,
                'message' => 'Usuario creado correctamente.',
            ], 201);

        } catch (\Throwable $e) {

            return response()->json([
                'message' => 'Error al guardar el usuario en la base de datos.',
                'errors'  => ['server' => [$e->getMessage()]],
            ], 500);
        }
    }

   public function usuariosEliminar($id)
    {
        try {
            DB::table('users')->where('id', $id)->delete();

            return response()->json(['ok' => true, 'message' => 'Usuario eliminado correctamente.']);

        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => 'Error al eliminar el usuario.'], 500);
        }
    }

    public function usuariosEditar($id)
    {
        $usuario = DB::table('users')->where('id', $id)->first();

        if (!$usuario) {
            return redirect()->back()->with('error', 'El usuario no existe.');
        }

        return view('pages.usuarios.editar', compact('usuario'));
    }

    public function usuariosEditarPost(Request $request, $id)
    {
        // VALIDACIONES
        $validator = Validator::make($request->all(), [
            'name'     => ['required','string','max:100'],
            'email'    => ['required','email'],
            'rol'      => ['required','integer'],
            'password' => ['nullable','string','min:4'], // OPCIONAL
        ], [
            'name.required'     => 'El nombre es obligatorio.',
            'email.required'    => 'El correo es obligatorio.',
            'email.email'       => 'Debes ingresar un correo válido.',
            'rol.required'      => 'Debes seleccionar un rol.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok'     => false,
                'errors' => $validator->errors(),
                'message'=> 'Revisa los campos del formulario.'
            ], 422);
        }

        try {

            // OBTENER USUARIO
            $usuario = DB::table('users')->where('id', $id)->first();

            if (!$usuario) {
                return response()->json([
                    'ok' => false,
                    'message' => 'El usuario no existe.'
                ], 404);
            }

            // SI PASSWORD VIENE VACÍA → NO CAMBIARLA
            $dataUpdate = [
                'name'       => $request->input('name'),
                'email'      => $request->input('email'),
                'rol'        => $request->input('rol'),
                'updated_at' => now(),
            ];

            if ($request->filled('password')) {
                $dataUpdate['password'] = Hash::make($request->input('password'));
            }

            // ACTUALIZAR
            DB::table('users')->where('id', $id)->update($dataUpdate);

            return response()->json([
                'ok'      => true,
                'message' => 'Usuario actualizado correctamente.',
                'redirect'=> url('usuarios/lista')
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'ok'      => false,
                'message' => 'Error al actualizar el usuario.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }





      public function listaUsuario()
    {   
        $usuarios = User::all();
        return view('pages.usuarios.lista', compact('usuarios'));
    }



     public function login()
    {
        return view('pages.usuarios.login');
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('usuarios', ['accion' => 'login']);
    }

    public function restablecerContraseña()
    {
        return view('pages.usuarios.restablecerContraseña');
    }

    
    
public function loginConfi(Request $request)
{
    try {
        $usuario = User::where('email', $request->input('email'))->first();

        if (!$usuario) {
            return response()->json(['ok' => false, 'mensaje' => 'El correo no está registrado.'], 401);
        }

        if (!Hash::check($request->input('password'), $usuario->password)) {
            return response()->json(['ok' => false, 'mensaje' => 'Contraseña incorrecta.'], 401);
        }

        if (!$usuario->activo) {
            return response()->json(['ok' => false, 'mensaje' => 'Tu cuenta está desactivada. Contacta al administrador.'], 403);
        }

        auth()->login($usuario, $request->boolean('remember'));

        $redirect = $usuario->rol === 2
            ? route('caja', ['accion' => 'ingresar'])
            : url('index');

        return response()->json([
            'ok'      => true,
            'mensaje' => 'Bienvenido, ' . $usuario->name,
            'redirect' => $redirect,
        ]);

    } catch (\Exception $e) {
        return response()->json(['ok' => false, 'mensaje' => 'Error interno en el servidor.'], 500);
    }
}

public function sendCode(Request $request)
{
    $request->validate(['email' => 'required|email']);
    $user = User::where('email', $request->email)->first();

    if (! $user) {
        return response()->json(['message' => 'Si el correo existe, recibirás un enlace.'], 200);
    }

    date_default_timezone_set('America/Costa_Rica');
    $fecha_actual = date('Y-m-d H:i:s');

    // 🔥 Token plano (no hash)
    $token = bin2hex(random_bytes(32));

    DB::table('users')
        ->where('email', $request->email)
        ->update([
            'reset_token' => $token,
            'reset_token_date' => $fecha_actual,
        ]);

    $url = url('/usuarios/restablecer?token=' . $token . '&email=' . $request->email);

    Mail::to($request->email)->send(new ResetCodeMail($url));

    return response()->json(['message' => 'Se ha enviado un enlace de recuperación a tu correo.']);
}



public function cambiarContrasena(Request $request)
{
    
        $request->validate([
        'email' => 'required|email',
        'token' => 'required',
        'password' => 'required|min:6'
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'ok' => false,
            'mensaje' => 'El correo no existe en el sistema.'
        ], 404);
    }

    // 🔥 Validar token correcto
    if ($user->reset_token !== $request->token) {
        return response()->json([
            'ok' => false,
            'mensaje' => 'El token no coincide o es incorrecto.'
        ], 401);
    }

    // 🔥 Validar expiración 30 minutos
    if (!$user->reset_token_date || now()->diffInMinutes($user->reset_token_date) > 30) {

        // 🔥 Limpiar token si expiró
        $user->reset_token = null;
        $user->reset_token_date = null;
        $user->save();

        return response()->json([
            'ok' => false,
            'mensaje' => 'El enlace ha expirado. Solicita uno nuevo.'
        ], 401);
    }

    $user->password = Hash::make($request->password);

    // 🔥 Limpiar token después de usarlo
    $user->reset_token = null;
    $user->reset_token_date = null;

    $user->save();

    return response()->json([
        'ok' => true,
        'mensaje' => 'La contraseña ha sido actualizada correctamente.'
    ]);
}

public function validarToken(Request $request)
{
    // Validar datos mínimos
    $request->validate([
        'email' => 'required|email',
        'token' => 'required'
    ]);

    // Buscar usuario
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'ok' => false,
            'mensaje' => 'El correo no existe en el sistema.'
        ], 404);
    }

    // Verificar que exista un token activo
    if (!$user->reset_token) {
        return response()->json([
            'ok' => false,
            'mensaje' => 'No existe un proceso de restablecimiento activo.'
        ], 400);
    }

    // Normalizar token (evita fallos por espacios o tipos)
    $tokenDB = trim((string) $user->reset_token);
    $tokenRequest = trim((string) $request->token);

    if ($tokenDB !== $tokenRequest) {
        return response()->json([
            'ok' => false,
            'mensaje' => 'El token no coincide o es incorrecto.'
        ], 401);
    }

    // Validar expiración 30 min
    if (!$user->reset_token_date || now()->diffInMinutes($user->reset_token_date) > 30) {

        // Limpiar token si expiró
        $user->reset_token = null;
        $user->reset_token_date = null;
        $user->save();

        return response()->json([
            'ok' => false,
            'mensaje' => 'El enlace ha expirado. Solicita uno nuevo.'
        ], 401);
    }

    // Todo correcto
    return response()->json([
        'ok' => true,
        'mensaje' => 'Token válido.'
    ]);
}

 
    

}