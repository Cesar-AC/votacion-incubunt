<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Asegúrate de importar tus modelos aquí si los necesitas, por ejemplo:
// use App\Models\Elecciones;
// use App\Models\Candidato;

class VotanteController extends Controller
{
    // Página principal del votante
    public function home()
    {
        return view('dashboard');
    }

    // ==========================================
    // SECCIÓN DE ELECCIONES
    // ==========================================

    // Listar todas las elecciones disponibles
    public function listarElecciones()
    {
        // $elecciones = Elecciones::where('estado', 'activa')->get();
        return view('votante.elecciones.index'); // Pasa compact('elecciones')
    }

    // Ver detalle de una elección antes de votar
    public function verDetalleEleccion($id)
    {
        return view('votante.elecciones.detalle', compact('id'));
    }

    // ==========================================
    // PROCESO DE VOTACIÓN
    // ==========================================

    // Vista inicial al entrar a votar
    public function iniciarVotacion($eleccionId)
    {
        return view('votante.votar.index', compact('eleccionId'));
    }

    // Listado de candidatos para elegir
    public function listarCandidatos($eleccionId)
    {
        return view('votante.votar.lista', compact('eleccionId'));
    }

    // Ver perfil de un candidato específico
    public function verDetalleCandidato($eleccionId, $candidatoId)
    {
        return view('votante.votar.detalle', compact('eleccionId', 'candidatoId'));
    }

    // Lógica para guardar el voto en la BD (POST)
    public function emitirVoto(Request $request, $eleccionId)
    {
        // Aquí va la lógica para guardar el voto
        // Voto::create([...]);

        return redirect()->route('votante.votar.exito', ['eleccionId' => $eleccionId]);
    }

    // Pantalla de "Gracias por votar"
    public function votoExitoso($eleccionId)
    {
        return view('votante.votar.exito', compact('eleccionId'));
    }

    // ==========================================
    // RESULTADOS
    // ==========================================
    
    public function verResultados($eleccionId)
    {
        return view('votante.resultados', compact('eleccionId'));
    }
}