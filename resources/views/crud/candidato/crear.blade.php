@extends('layouts.admin')

@section('content')
<div class="container-fluid"
    x-data="{
            tipos: {individual: 'Individual', grupal: 'Miembro de Partido'},
            areas: {{json_encode($areas->pluck('area', 'idArea'))}},
            usuarios: {
                @foreach($usuarios as $usuario)
                    @if($usuario->perfil?->obtenerNombreApellido() != null)
                        {{ $usuario->idUser }}: '{{ $usuario->perfil?->obtenerNombreApellido() . ' <' . $usuario->correo . '>' }}',
                    @else
                        {{ $usuario->idUser }}: '{{ $usuario->correo }}',
                    @endif
                @endforeach
            },
            partidos: {{json_encode($partidosPorEleccion)}},
            cargosPorArea: {{json_encode($cargosPorArea)}},
            cargosPresidencia: {{json_encode($cargosPresidencia->pluck('cargo', 'idCargo'))}},
        }">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Crear Candidatos</h1>
        <a href="{{ route('crud.candidato.ver') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    {{-- Mensajes de error, advertencia y éxito --}}
    @include('components.error-message')

    {{-- Seleccionar Elección --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="form-group">
                <label for="eleccion">Elección</label>
                <select class="form-control" id="eleccion" name="idEleccion" form="formCandidatoTemp" required x-model="$store.candidatos.idEleccion">
                    <option value="">Seleccione elección</option>
                    @foreach($elecciones as $eleccion)
                    <option value="{{ $eleccion->getKey() }}">
                        {{ $eleccion->titulo }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Formulario temporal --}}
    <div class="card shadow mb-4">
        <div class="card-body" x-data="{
            candidato: {
                idTipo: null,
                idArea: null,
                idCargo: null,
                idUsuario: null,
                idPartido: null,
                planTrabajo: null,
            },
        }">
            <form id="formCandidatoTemp" onsubmit="return false;">
                {{-- Tipo de Candidato --}}
                <div class="form-group">
                    <label for="tipoCandidato">Tipo de Candidato</label>
                    <select class="form-control" id="tipoCandidato" x-model="candidato.idTipo" required @change="candidato.idUsuario = null; candidato.idArea = null; candidato.idCargo = null; candidato.idPartido = null; candidato.planTrabajo = null;">
                        <option value="">Seleccione tipo</option>
                        <template x-for="(tipo, key) in tipos" :key="key">
                            <option :value="key" x-text="tipo"></option>
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
                                <option :value="key" x-text="area"></option>
                            </template>
                        </select>
                    </div>

                    <div class="form-group" x-show="candidato.idArea != null">
                        <label for="cargoIndividual">Cargo a postular</label>
                        <select class="form-control" id="cargoIndividual" x-model="candidato.idCargo">
                            <option value="">Seleccione cargo</option>
                            <template x-for="(cargos, areaId) in cargosPorArea[candidato.idArea]">
                                <option :value="cargos['idCargo']" x-text="cargos['cargo']"></option>
                            </template>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="usuarioIndividual">Persona que postula</label>
                        <select class="form-control" id="usuarioIndividual" x-model="candidato.idUsuario">
                            <option value="">Seleccione usuario</option>
                            <template x-for="(usuario, idUsuario) in usuarios" :key="idUsuario">
                                <option :value="idUsuario" x-text="usuario"></option>
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
                            <template x-for="(partido, idPartido) in partidos[$store.candidatos.idEleccion]" :key="idPartido">
                                <option :value="idPartido" x-text="partido"></option>
                            </template>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="usuarioIndividual">Persona que postula</label>
                        <select class="form-control" id="usuarioIndividual" x-model="candidato.idUsuario">
                            <option value="">Seleccione usuario</option>
                            <template x-for="(usuario, idUsuario) in usuarios" :key="idUsuario">
                                <option :value="idUsuario" x-text="usuario"></option>
                            </template>
                        </select>
                    </div>

                    <div class="form-group" id="cargoGrupalDiv">
                        <label for="cargoGrupal">Cargo al que postulan</label>
                        <select class="form-control" id="cargoGrupal" x-model="candidato.idCargo">
                            <option value="">Seleccione cargo</option>
                            <template x-for="(cargo, idCargo) in cargosPresidencia" :key="idCargo">
                                <option :value="idCargo" x-text="cargo"></option>
                            </template>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="planTrabajoGrupal">Enlace al plan de trabajo</label>
                        <input type="url" class="form-control" id="planTrabajoGrupal" x-model="candidato.planTrabajo" placeholder="Ingrese el enlace al plan de trabajo (opcional)">
                    </div>
                </div>

                <button type="button" class="btn btn-info mt-3" id="agregarCandidatoTemp" x-cloak x-show="candidato.idTipo && ((candidato.idArea && candidato.idCargo && candidato.idUsuario) || (candidato.idPartido && candidato.idUsuario))"
                    @click="$store.candidatos.candidatos.push({...candidato}); candidato = {idTipo: null, idUsuario: null, idArea: null, idCargo: null, idPartido: null, planTrabajo: null}">
                    <i class="fas fa-plus"></i> Agregar candidato
                </button>
            </form>
        </div>
    </div>

    {{-- Tabla temporal --}}
    <div class="card shadow mb-4">
        <div class="card-body" x-data>
            <h5 class="mb-3">Candidatos temporales</h5>
            <table class="table table-bordered" id="tablaCandidatosTemp">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Usuario</th>
                        <th>Partido</th>
                        <th>Área</th>
                        <th>Cargo</th>
                        <th>Plan Trabajo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(candidato, index) in $store.candidatos.candidatos" :key="index">
                        <tr>
                            <td x-text="tipos[candidato.idTipo]"></td>
                            <td x-text="usuarios[candidato.idUsuario]"></td>
                            <td x-text="partidos[candidato.idPartido] || 'N/A'"></td>
                            <td x-show="candidato.idTipo == 'individual'" x-text="areas[candidato.idArea]"></td>
                            <td x-show="candidato.idTipo == 'grupal'">Presidencia</td>
                            <td x-show="candidato.idTipo == 'individual'" x-text="cargosPorArea[candidato.idArea].filter(cargo => cargo.idCargo == candidato.idCargo)[0].cargo"></td>
                            <td x-show="candidato.idTipo == 'grupal'" x-text="cargosPresidencia[candidato.idCargo]"></td>
                            <td x-text="candidato.planTrabajo"></td>
                            <td>
                                <button type="button" class="btn btn-danger" @click="$store.candidatos.candidatos.splice(index, 1)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <button type="button" class="btn btn-success" id="guardarTodos" @click="$refs.formCandidatos.submit()">
                <i class="fas fa-save"></i> Guardar todos
            </button>
        </div>
    </div>

    <form action="{{ route('crud.candidato.crear') }}" method="post" x-ref="formCandidatos">
        @csrf
        <input type="hidden" name="idEleccion" x-model="$store.candidatos.idEleccion">
        <template x-for="(candidato, index) in $store.candidatos.candidatos" :key="index">
            <div>
                <input type="hidden" :name="'candidatos[' + index + '][tipo]'" :value="candidato.idTipo">
                <input type="hidden" :name="'candidatos[' + index + '][idUsuario]'" :value="candidato.idUsuario">
                <input type="hidden" :name="'candidatos[' + index + '][idCargo]'" :value="candidato.idCargo">
                <input type="hidden" :name="'candidatos[' + index + '][idPartido]'" :value="candidato.idPartido">
                <input type="hidden" :name="'candidatos[' + index + '][planTrabajo]'" :value="candidato.planTrabajo">
            </div>
        </template>
    </form>
</div>



<script>
document.addEventListener('alpine:init', function () {
    Alpine.store('candidatos', {
        idEleccion: {{ $eleccionesService->obtenerEleccionActiva()->getKey() }},
        candidatos: [],
    })
});
</script>


@endsection
