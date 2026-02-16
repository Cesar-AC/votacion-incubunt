<script>
  const inputFoto = document.getElementById('{{ $idInput ?? "inputFoto" }}');
  const visualizacionFoto = document.getElementById('{{ $idVisualizacion ?? "visualizacionFoto" }}');

  inputFoto.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const img = document.getElementById('visualizacionFoto');
        img.src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  });
</script>