@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Crear Candidatos</h1>
        <a href="{{ route('crud.candidato.ver') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    {{-- Seleccionar Elección --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="form-group">
                <label for="eleccion">Elección</label>
                <select class="form-control" id="eleccion" name="idEleccion" required>
    <option value="">Seleccione elección</option>
    @foreach($elecciones as $eleccion)
        <option value="{{ $eleccion->idElecciones }}">
            {{ $eleccion->titulo }}
        </option>
    @endforeach
</select>
            </div>
        </div>
    </div>

    {{-- Formulario temporal --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="formCandidatoTemp" onsubmit="return false;">
                @csrf

                {{-- Tipo de Candidato --}}
                <div class="form-group">
                    <label for="tipoCandidato">Tipo de Candidato</label>
                    <select class="form-control" id="tipoCandidato" required>
                        <option value="">Seleccione tipo</option>
                        <option value="individual">Individual (Director de Área)</option>
                        <option value="grupal">Grupal (Junta Directiva)</option>
                    </select>
                </div>

                {{-- Sección Individual --}}
                <div id="seccionIndividual" style="display:none;">
                    <div class="form-group">
                        <label for="cargoIndividual">Rol de postulación</label>
                        <select class="form-control" id="cargoIndividual">
    <option value="">Seleccione rol</option>
    @foreach($cargos as $cargo)
        @if($cargo->idArea != 1)
            <option value="{{ $cargo->idCargo }}">
                {{ $cargo->cargo }} - {{ $cargo->area->area }}
            </option>
        @endif
    @endforeach
</select>

                    </div>

                    <div class="form-group">
                        <label for="usuarioIndividual">Persona que postula</label>
                        <select class="form-control" id="usuarioIndividual">
                            <option value="">Seleccione usuario</option>
                            @foreach($usuarios as $usuario)
                               <option value="{{ $usuario->idUser }}">
    {{ $usuario->perfil->nombre ?? $usuario->correo }}
</option>

                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="planTrabajoIndividual">Plan de Trabajo</label>
                        <textarea class="form-control" id="planTrabajoIndividual" rows="3" placeholder="Ingrese el plan de trabajo (opcional)"></textarea>
                    </div>
                </div>

                {{-- Sección Grupal --}}
                <div id="seccionGrupal" style="display:none;">
                    <div class="form-group">
                        <label for="partido">Partido</label>
                        <select class="form-control" id="partido">
                            <option value="">Seleccione partido</option>
                            @foreach($partidos as $partido)
                                @if($partido->tipo === 'LISTA')
                                <option value="{{ $partido->idPartido }}">{{ $partido->partido }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Seleccionar miembros</label>
                        <div class="form-check">
                            @foreach($usuarios as $usuario)
                                <input class="form-check-input usuarioGrupal"
       type="checkbox"
       value="{{ $usuario->idUser }}"
       id="usuario{{ $usuario->idUser }}">

                               <label for="usuario{{ $usuario->idUser }}">

                                   {{ $usuario->perfil->nombre ?? $usuario->correo }}
                                </label><br>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group" id="cargoGrupalDiv" style="display:none;">
                        <label for="cargoGrupal">Cargo al que postulan</label>
                        <select class="form-control" id="cargoGrupal">
    <option value="">Seleccione cargo</option>
    @foreach($cargos as $cargo)
        @if($cargo->idArea == 1)
            <option value="{{ $cargo->idCargo }}">
                {{ $cargo->cargo }}
            </option>
        @endif
    @endforeach
</select>

                    </div>

                    <div class="form-group">
                        <label for="planTrabajoGrupal">Plan de Trabajo</label>
                        <textarea class="form-control" id="planTrabajoGrupal" rows="3" placeholder="Ingrese el plan de trabajo (opcional)"></textarea>
                    </div>
                </div>

                <button type="button" class="btn btn-info mt-3" id="agregarCandidatoTemp">
                    <i class="fas fa-plus"></i> Agregar candidato
                </button>
            </form>
        </div>
    </div>

    {{-- Tabla temporal --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5 class="mb-3">Candidatos temporales</h5>
            <table class="table table-bordered" id="tablaCandidatosTemp">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Usuario</th>
                        <th>Partido / Rol</th>
                        <th>Cargo</th>
                        <th>Plan Trabajo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Se llenará dinámicamente con JS --}}
                </tbody>
            </table>
            <button type="button" class="btn btn-success" id="guardarTodos">
                <i class="fas fa-save"></i> Guardar todos
            </button>
        </div>
    </div>
</div>


<script>
const tipoCandidato = document.getElementById('tipoCandidato');
const seccionIndividual = document.getElementById('seccionIndividual');
const seccionGrupal = document.getElementById('seccionGrupal');
const cargoGrupalDiv = document.getElementById('cargoGrupalDiv');
const usuarioGrupalCheckboxes = document.querySelectorAll('.usuarioGrupal');

const tablaCandidatosTemp = document.querySelector('#tablaCandidatosTemp tbody');
let candidatosTemp = [];

/* ===============================
   Mostrar / ocultar secciones
================================ */
tipoCandidato.addEventListener('change', function () {
    seccionIndividual.style.display = this.value === 'individual' ? 'block' : 'none';
    seccionGrupal.style.display = this.value === 'grupal' ? 'block' : 'none';
});

/* ===============================
   Mostrar cargo grupal
================================ */
usuarioGrupalCheckboxes.forEach(chk => {
    chk.addEventListener('change', function () {
        const anyChecked = Array.from(usuarioGrupalCheckboxes).some(c => c.checked);
        cargoGrupalDiv.style.display = anyChecked ? 'block' : 'none';
    });
});

/* ===============================
   Agregar candidato temporal
================================ */
document.getElementById('agregarCandidatoTemp').addEventListener('click', function () {

    const tipo = tipoCandidato.value;

    if (!tipo) {
        alert('Seleccione tipo de candidato');
        return;
    }

    /* -------- INDIVIDUAL -------- */
    if (tipo === 'individual') {

        const usuario = document.getElementById('usuarioIndividual');
        const cargo = document.getElementById('cargoIndividual');
        const planTrabajo = document.getElementById('planTrabajoIndividual').value;

        if (!usuario.value || !cargo.value) {
            alert('Seleccione usuario y cargo');
            return;
        }

        candidatosTemp.push({
            tipo: 'Individual',
            idUsuario: usuario.value,
            usuarioTexto: usuario.options[usuario.selectedIndex].text,
            idCargo: cargo.value,
            cargoTexto: cargo.options[cargo.selectedIndex].text,
            partidoTexto: '—',
            planTrabajo: planTrabajo || '—'
        });

        renderTabla();
        return;
    }

    /* -------- GRUPAL -------- */
    if (tipo === 'grupal') {

        const partido = document.getElementById('partido');
        const cargo = document.getElementById('cargoGrupal');
        const planTrabajo = document.getElementById('planTrabajoGrupal').value;
        const usuariosChecked = Array.from(usuarioGrupalCheckboxes).filter(c => c.checked);

        if (!partido.value || !cargo.value || usuariosChecked.length === 0) {
            alert('Seleccione partido, cargo y miembros');
            return;
        }

        usuariosChecked.forEach(u => {
            const label = document.querySelector(`label[for="${u.id}"]`).innerText;

            candidatosTemp.push({
                tipo: 'Grupal',
                idUsuario: u.value,
                usuarioTexto: label,
                idCargo: cargo.value,
                cargoTexto: cargo.options[cargo.selectedIndex].text,
                idPartido: partido.value,
                partidoTexto: partido.options[partido.selectedIndex].text,
                planTrabajo: planTrabajo || '—'
            });
        });

        renderTabla();
    }
});

/* ===============================
   Renderizar tabla
================================ */
function renderTabla() {
    tablaCandidatosTemp.innerHTML = '';

    candidatosTemp.forEach((c, i) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${c.tipo}</td>
            <td>${c.usuarioTexto}</td>
            <td>${c.partidoTexto}</td>
            <td>${c.cargoTexto}</td>
            <td><small>${c.planTrabajo}</small></td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="eliminarTemp(${i})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tablaCandidatosTemp.appendChild(tr);
    });
}

/* ===============================
   Eliminar temporal
================================ */
function eliminarTemp(index) {
    candidatosTemp.splice(index, 1);
    renderTabla();
}

/* ===============================
   Guardar todos
================================ */
document.getElementById('guardarTodos').addEventListener('click', function () {

    const eleccionSelect = document.getElementById('eleccion');
    const idEleccion = eleccionSelect.value;


    if (!idEleccion) {
        alert('Seleccione elección');
        return;
    }

    if (candidatosTemp.length === 0) {
        alert('No hay candidatos agregados');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("crud.candidato.crear") }}';
    form.style.display = 'none';

    form.innerHTML += `@csrf`;
    form.innerHTML += `<input type="hidden" name="idEleccion" value="${idEleccion}">`;
    

    candidatosTemp.forEach((c, i) => {
        form.innerHTML += `<input type="hidden" name="candidatos[${i}][tipo]" value="${c.tipo}">`;
        form.innerHTML += `<input type="hidden" name="candidatos[${i}][idUsuario]" value="${c.idUsuario}">`;
        form.innerHTML += `<input type="hidden" name="candidatos[${i}][idCargo]" value="${c.idCargo}">`;
        form.innerHTML += `<input type="hidden" name="candidatos[${i}][planTrabajo]" value="${c.planTrabajo === '—' ? '' : c.planTrabajo}">`;

        if (c.idPartido) {
            form.innerHTML += `<input type="hidden" name="candidatos[${i}][idPartido]" value="${c.idPartido}">`;
        }
    });

    document.body.appendChild(form);
    form.submit();
});
</script>


@endsection
