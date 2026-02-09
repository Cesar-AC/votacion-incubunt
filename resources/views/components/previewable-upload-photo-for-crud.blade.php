<!-- FOTO -->
<div class="card shadow-sm mb-4" x-data="{inputFoto: null}">
    <div class="card-body">
        <h6 class="font-weight-bold mb-3">Foto</h6>

        <div class="form-group">
            <input
            type="file"
            x-ref="inputFoto"
            name="foto"
            id="inputFoto"
            class="form-control-file @error('foto') is-invalid @enderror"
            accept=".png, .jpg, .jpeg, .gif"
            x-model="inputFoto"
            value="">
            @error('foto')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div id="fotoPreview" class="mt-3" @if (!isset($foto)) x-cloak x-show="inputFoto != null" @endif>
            <p class="text-muted mb-2">Previsualización:</p>
            <img id="visualizacionFoto" src="{{ isset($foto) ? $foto : '' }}" alt="Previsualización de foto" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
        </div>
    </div>
</div>

@push('scripts')
@include('components.preview-upload-photo-script')
@endpush