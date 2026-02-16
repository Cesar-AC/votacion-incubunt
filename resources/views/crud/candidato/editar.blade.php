@extends('layouts.admin')

@section('content')
<div class="container-fluid"
    x-data="{
            tipos: {individual: 'Individual', grupal: 'Miembro de Partido'},
            elecciones: {{json_encode($elecciones->pluck('titulo', 'idElecciones'))}},
            areas: {{json_encode($areas->pluck('area', 'idArea'))}},
            partidos: {{json_encode($partidos->pluck('partido', 'idPartido'))}},
            cargosPorArea: {{json_encode($cargosPorArea)}},
            cargosPresidencia: {{json_encode($cargosPresidencia->pluck('cargo', 'idCargo'))}},
            idEleccion: {{ $eleccion->getKey() }},
            candidato: {
                idCandidato: '{{ $candidato->getKey() }}',
                idTipo: '{{ $candidatoEleccion->idPartido == null ? 'individual' : 'grupal' }}',
                idArea: '{{ $candidatoEleccion->cargo->area->getKey() }}',
                idCargo: '{{ $candidatoEleccion->cargo->getKey() }}',
                idPartido: '{{ $candidatoEleccion->partido?->getKey() }}',
                planTrabajo: '{{ $candidato->planTrabajo }}'
            }
        }">

    @include('components.error-message')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Editar Candidato</h1>
        <a href="{{ route('crud.candidato.ver') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="formCandidatoTemp" onsubmit="return false;">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="idCandidato">Candidato</label>
                    <select class="form-control" id="idCandidato" disabled>
                        <option value="" selected>{{ $candidato->usuario->perfil?->obtenerNombreApellido() . ' <' . $candidato->usuario->correo . '>' }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="idEleccion">Elección</label>
                    <select class="form-control" id="idEleccion" x-model="idEleccion">
                        <option value="">Seleccione elección</option>
                        <template x-for="(eleccion, id) in elecciones" :key="id">
                            <option :value="id" x-text="eleccion" :selected="id == idEleccion"></option>
                        </template>
                    </select>
                </div>

                {{-- Tipo de Candidato --}}
                <div class="form-group">
                    <label for="tipoCandidato">Tipo de Candidato</label>
                    <select class="form-control" id="tipoCandidato" x-model="candidato.idTipo" required @change="candidato.idUsuario = null; candidato.idArea = null; candidato.idCargo = null; candidato.idPartido = null; candidato.planTrabajo = null;">
                        <option value="">Seleccione tipo</option>
                        <template x-for="(tipo, key) in tipos" :key="key">
                            <option :value="key" x-text="tipo" :selected="key == candidato.idTipo"></option>
                        </template>
                    </select>
                </div>

                {{-- Sección Individual --}}
                <div id="seccionIndividual" x-cloak x-show="candidato.idTipo == 'individual'">
                    <div class="form-group">
                        <label for="areaIndividual">Área a postular</label>
                        <select class="form-control" id="areaIndividual" x-model="candidato.idArea">
                            <option value="">Seleccione área</option>
                            <template x-for="(area, key) in areas" :key="key">
                                <option :value="key" x-text="area" :selected="key == candidato.idArea"></option>
                            </template>
                        </select>
                    </div>

                    <div class="form-group" x-show="candidato.idArea != null">
                        <label for="cargoIndividual">Cargo a postular</label>
                        <select class="form-control" id="cargoIndividual" x-model="candidato.idCargo">
                            <option value="">Seleccione cargo</option>
                            <template x-for="(cargos, areaId) in cargosPorArea[candidato.idArea]">
                                <option :value="cargos['idCargo']" x-text="cargos['cargo']" :selected="cargos['idCargo'] == candidato.idCargo"></option>
                            </template>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="planTrabajoIndividual">Enlace al plan de trabajo</label>
                        <input type="url" x-model="candidato.planTrabajo" class="form-control" id="planTrabajoIndividual" placeholder="Ingrese el enlace al plan de trabajo (opcional)">
                    </div>
                </div>

                {{-- Sección Grupal --}}
                <div id="seccionGrupal" x-cloak x-show="candidato.idTipo == 'grupal'">
                    <div class="form-group">
                        <label for="partido">Partido</label>
                        <select class="form-control" id="partido" x-model="candidato.idPartido">
                            <option value="">Seleccione partido</option>
                            <template x-for="(partido, idPartido) in partidos" :key="idPartido">
                                <option :value="idPartido" x-text="partido" :selected="idPartido == candidato.idPartido"></option>
                            </template>
                        </select>
                    </div>

                    <div class="form-group" id="cargoGrupalDiv">
                        <label for="cargoGrupal">Cargo al que postulan</label>
                        <select class="form-control" id="cargoGrupal" x-model="candidato.idCargo">
                            <option value="">Seleccione cargo</option>
                            <template x-for="(cargo, idCargo) in cargosPresidencia" :key="idCargo">
                                <option :value="idCargo" x-text="cargo" :selected="idCargo == candidato.idCargo"></option>
                            </template>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="planTrabajoGrupal">Enlace al plan de trabajo</label>
                        <input type="url" class="form-control" id="planTrabajoGrupal" x-model="candidato.planTrabajo" placeholder="Ingrese el enlace al plan de trabajo (opcional)">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" @click="$refs.formEditar.submit()">
                    <i class="fas fa-save"></i> Actualizar Candidato
                </button>
            </form>

            <form class="hidden" x-ref="formEditar" action="{{ route('crud.candidato.editar', [$eleccion->getKey(), $candidato->getKey()]) }}" method="post">
                @csrf
                @method('PUT')
                <input type="hidden" name="tipo" x-model="candidato.idTipo">
                <input type="hidden" name="idArea" x-model="candidato.idArea">
                <input type="hidden" name="idCargo" x-model="candidato.idCargo">
                <input type="hidden" name="idPartido" x-model="candidato.idPartido">
                <input type="hidden" name="planTrabajo" x-model="candidato.planTrabajo">
            </form>
        </div>
    </div>
</div>
@endsection
