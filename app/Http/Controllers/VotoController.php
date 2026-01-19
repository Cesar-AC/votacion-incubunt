<?php

namespace App\Http\Controllers;

use App\Interfaces\Services\IVotoService;
use App\Models\Voto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class VotoController extends Controller
{
    public function __construct(
        protected readonly IVotoService $votoService,
    ) {}

    public function index()
    {
        $votos = Voto::with('candidato.usuario.perfil')->get();
        return view('crud.voto.ver', compact('votos'));
    }

    public function create()
    {
        return view('crud.voto.crear');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idCandidato' => 'required|integer'
        ]);

        try {
            $this->votoService->votar(Auth::user(), $data['idCandidato']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'success' => true,
            'message' => 'Voto creado',
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return void
     * Un alias para la funcionalidad de store
     */

    public function votar(Request $request)
    {
        return $this->store($request);
    }
}
