<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Voto - {{ $eleccion->titulo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #1e3a8a;
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .header h2 {
            color: #3b82f6;
            font-size: 18px;
            font-weight: normal;
        }
        
        .info-box {
            background-color: #eff6ff;
            border: 2px solid #3b82f6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            color: #1e40af;
        }
        
        .section-title {
            background-color: #1e40af;
            color: white;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        
        .vote-item {
            background-color: #f9fafb;
            border-left: 4px solid #3b82f6;
            padding: 12px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        
        .vote-item-header {
            font-weight: bold;
            color: #1e40af;
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        
        .candidate-name {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 4px;
        }
        
        .candidate-info {
            font-size: 11px;
            color: #6b7280;
        }
        
        .party-box {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .party-name {
            font-size: 18px;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            font-size: 10px;
            color: #6b7280;
            text-align: center;
        }
        
        .important-notice {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 8px;
            padding: 12px;
            margin-top: 20px;
            font-size: 11px;
        }
        
        .important-notice strong {
            color: #b45309;
        }
        
        .verification-code {
            background-color: #f3f4f6;
            border: 2px dashed #9ca3af;
            padding: 10px;
            margin: 20px 0;
            text-align: center;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>COMPROBANTE DE VOTO</h1>
        <h2>{{ $eleccion->titulo }}</h2>
    </div>
    
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Votante:</span>
            <span>{{ $user->perfil->nombre ?? 'N/A' }} {{ $user->perfil->apellidoPaterno ?? '' }} {{ $user->perfil->apellidoMaterno ?? '' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">DNI:</span>
            <span>{{ $user->perfil->dni ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Carrera:</span>
            <span>{{ $user->perfil->carrera->carrera ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de votación:</span>
            <span>{{ now()->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Elección:</span>
            <span>{{ $eleccion->titulo }}</span>
        </div>
    </div>
    
    <div class="verification-code">
        CÓDIGO DE VERIFICACIÓN: {{ strtoupper(substr(md5($user->id . $eleccion->idElecciones . now()->timestamp), 0, 16)) }}
    </div>
    
    @if($votosPartido->isNotEmpty())
    <div class="section-title">VOTO A PARTIDO POLÍTICO</div>
    @foreach($votosPartido as $voto)
    <div class="party-box">
        <div class="party-name">{{ $voto->partido->partido ?? 'Partido' }}</div>
        @if($voto->partido->descripcion)
        <div style="margin-top: 8px; font-size: 12px; font-weight: normal;">
            {{ $voto->partido->descripcion }}
        </div>
        @endif
    </div>
    @endforeach
    @endif
    
    @if($votosCandidato->isNotEmpty())
    <div class="section-title">VOTOS A CANDIDATOS</div>
    @foreach($votosCandidato as $voto)
    @if($voto->candidato)
        @php
            $candidatoEleccion = $voto->candidato->candidatoElecciones()->where('idElecciones', $eleccion->idElecciones)->first();
        @endphp
        <div class="vote-item">
            <div class="vote-item-header">
                {{ $candidatoEleccion && $candidatoEleccion->cargo ? $candidatoEleccion->cargo->cargo : 'Cargo desconocido' }}
            </div>
            <div class="candidate-name">
                {{ $voto->candidato->usuario->perfil->nombre ?? 'Sin nombre' }}
                {{ $voto->candidato->usuario->perfil->apellidoPaterno ?? '' }}
                {{ $voto->candidato->usuario->perfil->apellidoMaterno ?? '' }}
            </div>
            <div class="candidate-info">
                @if($candidatoEleccion && $candidatoEleccion->partido)
                    Partido: {{ $candidatoEleccion->partido->partido }}
                @else
                    Candidato Independiente
                @endif
                @if($voto->candidato->usuario->perfil->carrera)
                    | Carrera: {{ $voto->candidato->usuario->perfil->carrera->carrera }}
                @endif
            </div>
        </div>
    @endif
    @endforeach
    @endif
    
    <div class="important-notice">
        <strong>IMPORTANTE:</strong> Este comprobante es un documento personal que certifica tu participación en el proceso electoral. 
        Tu voto ha sido registrado de forma segura y anónima. No podrás modificar tu voto una vez emitido. 
        Los resultados estarán disponibles una vez finalice el proceso electoral.
    </div>
    
    <div class="footer">
        <p><strong>Sistema de Votación Electrónica - INCUBUNT</strong></p>
        <p>Documento generado automáticamente el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Este comprobante no tiene validez legal fuera del contexto del proceso electoral interno.</p>
    </div>
</body>
</html>
