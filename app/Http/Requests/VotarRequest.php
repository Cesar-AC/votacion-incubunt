<?php

namespace App\Http\Requests;

use App\Interfaces\Services\IVotoService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class VotarRequest extends FormRequest
{
    public function __construct(
        protected IVotoService $votoService,
    ) {}

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $votante = Auth::user();
        return $this->votoService->puedeVotar($votante);
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
