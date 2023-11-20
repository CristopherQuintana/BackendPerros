<?php

namespace App\Http\Controllers;
use App\Repositories\PerroRepository;
use App\Requests\PerroRequest;
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

    public function actualizarPerro(PerroRequest $request, $id)
    {
        // Actualizar un perro existente
        return $this->perroRepository->updatePerro($id, $request);
    }

    public function borrarPerro($id)
    {
        // Eliminar un perro por su ID
        return $this->perroRepository->deletePerro($id);
    }
}
