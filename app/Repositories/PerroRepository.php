<?php

// app/Repositories/PerroRepository.php

namespace App\Repositories;

use App\Models\Perro;
use App\Models\Interaccion;

class PerroRepository
{
    public function createPerro($request)
    {
        try {
            $perro = Perro::create([
                'nombre' => $request->nombre,
                'foto_url' => $request->foto,
                'descripcion' => $request->descripcion,
            ]);

            if (!$perro) {
                return response()->json(['message' => 'Perro no creado'], 404);
            }

            return response()->json($perro, 201);

        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    public function readPerro($id)
    {
        try {
            $perro = Perro::find($id);

            if (!$perro) {
                return response()->json(['message' => 'Perro no encontrado'], 404);
            }

            return response()->json($perro, 200);

        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    public function updatePerro($id, $request)
    {
        try {
            $perro = Perro::find($id);
            // Obtener los atributos de la solicitud que están presentes
            $atributos = array_filter([
                'nombre' => $request->nombre ?? null,
                'foto_url' => $request->foto ?? null,
                'descripcion' => $request->descripcion ?? null,
            ]);
            // Actualizar el perro solo con los atributos presentes
            $perro->update($atributos);
            if (!$perro) {
                return response()->json(['message' => 'Perro no encontrado'], 404);
            }
            return response()->json($perro, 201);
        } catch (Exception $e) {
            $this->handleException($e);
        }
        
    }

    public function deletePerro($id)
    {
        try {
            $perro = Perro::find($id);
            if (!$perro) {
                return response()->json(['message' => 'Perro no encontrado'], 404);
            }
            $perro->delete();
            return response()->json(['message' => 'Perro eliminado correctamente'], 201);
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    public function obtenerPerroRandom()
    {
        try {
            $perro = Perro::inRandomOrder()->select('id', 'nombre')->first();

            if (!$perro) {
                return response()->json(['message' => 'No hay Perros'], 404);
            }

            return response()->json($perro, 200);
        } catch (Exception $e) {
            $this->handleException($e);
        }
        
    }

    public function obtenerPerrosCandidatos($id)
    {
        try {
            $perros = Perro::inRandomOrder()->where('id','!=',$id)->take(5)->get();

            if ($perros->isEmpty()) {
                return response()->json(['message' => 'No hay perros'], 404);
            }

            return response()->json($perros, 200);
        } catch (Exception $e) {
            $this->handleException($e);
        }
        
    }

    public function guardarPreferencias($idInteresado, $request)
    {
        try{
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
        } catch (Exception $e) {
            $this->handleException($e);
        }
        
    }

    public function verPerros($id, $interes){
        // Obtener los perros interesados o no
        try {
            $perros = Perro::join('interacciones', function ($join) use ($id, $interes) {
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
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    public function verPerrosAceptados($id){
        return $this->verPerros($id, 'aceptado');
    }

    public function verPerrosRechazados($id){
        return $this->verPerros($id, 'rechazado');
    }

    private function handleException(Exception $e)
    {
        Log::info([
            "error" => $e->getMessage(),
            "linea" => $e->getLine(),
            "file" => $e->getFile(),
            "metodo" => __METHOD__
        ]);

        return response()->json([
            "error" => $e->getMessage(),
            "linea" => $e->getLine(),
            "file" => $e->getFile(),
            "metodo" => __METHOD__
        ], Response::HTTP_BAD_REQUEST);
    }
}