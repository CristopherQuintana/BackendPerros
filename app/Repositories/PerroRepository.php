<?php

// app/Repositories/PerroRepository.php

namespace App\Repositories;

use App\Models\Perro;

class PerroRepository
{
    public function createPerro($request)
    {
        $perro = Perro::create([
            'nombre' => $request->nombre,
            'foto_url' => $request->foto,
            'descripcion' => $request->descripcion,
        ]);

        if (!$perro) {
            return response()->json(['message' => 'Perro no encontrado'], 404);
        }
        return response()->json($perro, 201);
        
    }

    public function readPerro($id)
    {
        $perro = Perro::find($id);
        if (!$perro) {
            return response()->json(['message' => 'Perro no encontrado'], 404);
        }
        return response()->json($perro, 200);
    }

    public function updatePerro($id, $request)
    {
        $perro = Perro::find($id);
        $perro->update([
            'nombre' => $request->nombre,
            'foto_url' => $request->foto,
            'descripcion' => $request->descripcion,
        ]);
        if (!$perro) {
            return response()->json(['message' => 'Perro no encontrado'], 404);
        }
        return response()->json($perro, 201);
    }

    public function deletePerro($id)
    {
        $perro = Perro::find($id);
        if (!$perro) {
            return response()->json(['message' => 'Perro no encontrado'], 404);
        }
        $perro->delete();
        return response()->json(['message' => 'Perro eliminado correctamente'], 201);
    }

    public function obtenerPerroRandom()
    {
        $perro = Perro::inRandomOrder()->select('id', 'nombre')->first();

        if (!$perro) {
            return response()->json(['message' => 'No hay Perros'], 404);
        }

        return response()->json($perro, 200);
    }

    public function obtenerPerrosCandidatos($id)
    {
        $perros = Perro::inRandomOrder()->select('id')->where('id','!=',$id)->take(5)->get();

        if ($perros->isEmpty()) {
            return response()->json(['message' => 'No hay perros'], 404);
        }

        return response()->json($perros, 200);
    }

    public function guardarPreferencias(Request $request, $idInteresado)
    {
        // Obtener el perro interesado
        $perroInteresado = Perro::findOrFail($idInteresado);

        // Obtener los candidatos y preferencias de la solicitud
        $candidatos = $request->candidatos;
        $preferencias = $request->preferencias;
        $match = false;

        // Verificar que haya la misma cantidad de candidatos y preferencias
        if (count($candidatos) != count($preferencias)) {
            return response()->json(['message' => 'La cantidad de candidatos y preferencias no coincide'], 400);
        }

        // Iterar sobre los candidatos y preferencias para crear las interacciones
        foreach ($candidatos as $index => $candidatoId) {
            // Obtener el perro candidato actual
            $perroCandidato = Perro::findOrFail($candidatoId);

            // Crear una nueva instancia de la interacción con los datos proporcionados
            $interaccion = new Interaccion([
                'perro_interesado_id' => $perroInteresado->id,
                'perro_candidato_id' => $perroCandidato->id,
                'preferencia' => $preferencias[$index],
            ]);

            // Guardar la interacción en la base de datos
            $interaccion->save();

            // Verificar si hay un match
            if ($interaccion->preferencia === 'aceptado') {
                $interaccionInversa = Interaccion::where('perro_interesado_id', $perroCandidato->id)
                    ->where('perro_candidato_id', $perroInteresado->id)
                    ->where('preferencia', 'aceptado')
                    ->first();

                if ($interaccionInversa) {
                    $match = true;
                }
            }
        }

        // Puedes devolver una respuesta JSON si es necesario
        return response()->json(['message' => $match ? 'Hay match' : 'Ok'], 200);
    }

    public function verPerros($id, $interes){
        // Obtener los perros interesado
        $perros = Perro::join('interacciones', function ($join) use ($id) {
            $join->on('perros.id', '=', 'interacciones.perro_candidato_id')
                ->where('interacciones.perro_interesado_id', '=', $id)
                ->where('interacciones.preferencia', '=', $interes);
        })
        ->select('perros.*')
        ->get();

        if ($perros->isEmpty()) {
            return response()->json(['message' => 'No hay perros'], 404);
        }
        return response()->json($perros, 200);
    }

    public function verPerrosAceptados($id){
        return $this->verPerros($id, 'aceptado');
    }

    public function verPerrosRechazados($id){
        return $this->verPerros($id, 'rechazado');
    }
}