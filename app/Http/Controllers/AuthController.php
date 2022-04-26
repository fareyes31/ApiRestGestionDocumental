<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

use JWTAuth;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 120
        ]);
    }

    public function register(Request $request)
    {
        $validator = validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|string|email|max:100|unique:users',
            'password'=>'required|string|min:6',
        ]);

        if ( $validator ->fails()) {

            return response()->json(
                $validator->errors(),400);
            // [
            //     'error'=>'¡Usuario ya se encuentra Registrado!',
            // ],400);
        }

        $user = User::create(array_merge(
            $validator->validate(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message'=>'¡Usuario Registrado Exitosamente!',
            'user'=>$user
        ],201);
    }

    public function obtenerusuarios()
    {
       $users= User::all();
       return response()->json([
           'message'=>'¡Consulta Exitosa!',
           'users'=>$users
       ]);
    }

    public function searchuser(Request $request){
        $id=$request->id;
        if($id == ""){
            return response()->json(['error' => 'Debes enviar un identificador de usuario'], 403);
        }
        $user = User::find($id);
        dd($user);
        if($user == null){
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }else{
            return response()->json([
                'message'=>'¡Consulta Exitosa!',
                'users'=>$user
            ],200);
        }
    }

    public function validartoken(){
        return response()->json([
            'message'=>'¡Consulta Exitosa!',
        ],200);
    }
}
