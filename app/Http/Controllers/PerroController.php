<?php

namespace App\Http\Controllers;
use App\Repositories\PerroRepository;
use App\Http\Requests\PerroRequest;
use App\Http\Requests\EditPerroRequest;
use App\Http\Requests\InteraccionRequest;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PerroController extends Controller
{
    public function __construct(PerroRepository $perroRepository)
    {
        $this->perroRepository = $perroRepository;
    }

    public function verPerro($id)
    {
        // Obtener un perro por su ID
        return $this->perroRepository->readPerro($id);
    }

    public function guardarPerro(PerroRequest $request)
    {
        // Crear un nuevo perro
        return $this->perroRepository->createPerro($request);
    }

    public function actualizarPerro(EditPerroRequest $request, $id)
    {
        // Actualizar un perro existente
        return $this->perroRepository->updatePerro($id, $request);
    }

    public function borrarPerro($id)
    {
        // Eliminar un perro por su ID
        return $this->perroRepository->deletePerro($id);
    }

    public function obtenerPerroRandom()
    {
        // Obtiene un perro al azar
        return $this->perroRepository->obtenerPerroRandom();
    }

    public function obtenerPerrosCandidatos($id)
    {
        // Obtiene un perro candidato
        return $this->perroRepository->obtenerPerrosCandidatos($id);
    }

    public function guardarPreferencias(InteraccionRequest $request, $id)
    {
        return $this->perroRepository->guardarPreferencias($id, $request);
    }

    public function verPerrosAceptados($id)
    {
        return $this->perroRepository->verPerrosAceptados($id);
    }

    public function verPerrosRechazados($id)
    {
        return $this->perroRepository->verPerrosRechazados($id);
    }

}
