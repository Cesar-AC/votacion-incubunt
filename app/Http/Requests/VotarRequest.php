<?php

namespace App\Http\Requests;

use App\Enum\Permiso;
use App\Interfaces\Services\IEleccionesService;
use App\Interfaces\Services\IPermisoService;
use App\Interfaces\Services\IVotoService;
use App\Models\Candidato;
use App\Models\CandidatoEleccion;
use App\Models\Elecciones;
use App\Models\Interfaces\IElegibleAVoto;
use App\Models\Partido;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VotarRequest extends FormRequest
{
    public function __construct(
        protected IEleccionesService $eleccionesService,
        protected IPermisoService $permisoService,
        protected IVotoService $votoService,
    ) {}

    protected function tienePermisoVotar(User $usuario): bool
    {
        return $this->permisoService->comprobarUsuario($usuario, $this->permisoService->permisoDesdeEnum(Permiso::VOTO_VOTAR));
    }

    protected function estaUsuarioEnPadron(User $usuario, Elecciones $eleccion): bool
    {
        return $this->eleccionesService->estaEnPadronElectoral($usuario, $eleccion);
    }

    protected function perteneceEntidadAEleccion(IElegibleAVoto $entidad, Elecciones $eleccion): bool
    {
        return DB::table($entidad->obtenerTabla() . 'Eleccion')
            ->where('idElecciones', '=', $eleccion->getKey())
            ->where($entidad->obtenerNombrePK(), '=', $entidad->obtenerPK())
            ->exists();
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $votante = Auth::user();
        $eleccion = $this->eleccionesService->obtenerEleccionActiva();

        return $this->votoService->puedeVotar($votante, $eleccion);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'idPartido' => [
                'required',
                'integer',
                'exists:Partido,idPartido',
            ],
            'candidatos' => [
                'required',
                'array',
                'min:1',
            ],
            'candidatos.*' => [
                'required',
                'integer',
                'exists:Candidato,idCandidato',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'idPartido.required' => 'Debe seleccionar un partido político.',
            'idPartido.integer' => 'El partido debe ser un número válido.',
            'idPartido.exists' => 'El partido seleccionado no existe.',
            'candidatos.required' => 'Debe seleccionar al menos un candidato.',
            'candidatos.array' => 'Los candidatos deben ser una lista válida.',
            'candidatos.min' => 'Debe seleccionar al menos un candidato.',
            'candidatos.*.required' => 'Todos los candidatos deben ser válidos',
            'candidatos.*.integer' => 'Los IDs de candidatos deben ser números',
            'candidatos.*.exists' => 'Uno o más candidatos no existen',
        ];
    }
}
