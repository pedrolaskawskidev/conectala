<?php

namespace App\Http\Controllers;

use App\Models\Usuarios;
use Illuminate\Http\Request;

class UsuariosController extends Controller
{

    public function index()
    {
        return Usuarios::all();
    }

    public function store(Request $request)
    {
        try {
            Usuarios::firstOrCreate($request->all());

            return response()->json([
                'message' => 'Criado com sucesso',
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function show(string $id)
    {
        try {
            $usuario = Usuarios::findOrFail($id);

            return response()->json([
                'message' => $usuario,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Usuario nao encontrado',
            ], 404);
        }
    }


    public function update(Request $request, string $id)
    {
        try {
            $usuario = Usuarios::findOrFail($id);
            $usuario->update($request->all());

            return response()->json([
                'message' => $usuario,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Usuario nao encontrado',
            ], 404);
        }
    }


    public function destroy(string $id)
    {
        try {
            $usuario = Usuarios::findOrFail($id);
            $usuario->delete();

            return response()->json([
                'message' => "Usuario deletado",
                $usuario
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Usuario nao encontrado',
            ], 404);
        }
    }
}
