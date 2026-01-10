<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatos 2026 - Incubunt VOTE</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #1a5490 0%, #2463a8 100%);
            border-radius: 12px;
            padding: 25px 40px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .logo {
            background: #ffa500;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            color: #1a5490;
        }

        .header-title {
            color: white;
            font-size: 28px;
            font-weight: 600;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #1c3a5e 0%, #243f60 100%);
            border-radius: 12px;
            padding: 50px 40px;
            margin-bottom: 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .hero h1 {
            color: white;
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .hero p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 18px;
        }

        /* Section Title */
        .section-title {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 25px;
            padding-left: 5px;
        }

        /* Cards Container */
        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        /* Card */
        .card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.4);
        }

        .card-header {
            padding: 35px 30px;
            color: white;
        }

        .card-header.blue {
            background: linear-gradient(135deg, #4a90e2 0%, #5ba3f5 100%);
        }

        .card-header.orange {
            background: linear-gradient(135deg, #ffa500 0%, #ffb933 100%);
        }

        .card-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .card-subtitle {
            font-size: 16px;
            font-style: italic;
            opacity: 0.95;
        }

        .card-body {
            background: white;
            padding: 30px;
        }

        /* Avatar Group */
        .avatar-group {
            display: flex;
            margin-bottom: 20px;
        }

        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #d0d0d0;
            border: 3px solid white;
            margin-left: -12px;
            transition: transform 0.2s ease;
        }

        .avatar:first-child {
            margin-left: 0;
        }

        .avatar:hover {
            transform: scale(1.1);
            z-index: 10;
        }

        .avatar-more {
            background: #666;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
        }

        /* Card Description */
        .card-description {
            color: #555;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        /* Card Link */
        .card-link {
            color: #4a90e2;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: gap 0.3s ease;
        }

        .card-link:hover {
            gap: 10px;
        }

        .card-link.orange {
            color: #ff8c00;
        }

        /* Responsive */
        @media (max-width: 1100px) {
            .cards-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 36px;
            }

            .card-title {
                font-size: 24px;
            }

            .header {
                padding: 20px 25px;
            }

            .header-title {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="logo">ING</div>
            <h2 class="header-title">incubunt VOTE</h2>
        </header>

        <!-- Hero Section -->
        <section class="hero">
            <h1>Candidatos 2026</h1>
            <p>Conoce a quienes liderarán Incubunt.</p>
        </section>

        <!-- Candidates Section -->
        <div class="section-title">Postulación Presidencial</div>
        
        <div class="cards-container">
            <!-- Card 1: Partido A - Sinergia -->
            <article class="card">
                <div class="card-header blue">
                    <h3 class="card-title">Partido A - Sinergia</h3>
                    <p class="card-subtitle">"Innovación y Liderazgo"</p>
                </div>
                <div class="card-body">
                    <div class="avatar-group">
                        <div class="avatar"></div>
                        <div class="avatar"></div>
                        <div class="avatar"></div>
                        <div class="avatar avatar-more">+3</div>
                    </div>
                    <p class="card-description">
                        Somos un equipo multidisciplinario de la UNT comprometidos con potenciar el ecosistema emprendedor. Creemos en la fuerza de la unión entre facultades para crear líderes integrales.
                    </p>
                    <a href="#" class="card-link">Ver equipo y propuestas →</a>
                </div>
            </article>

            <!-- Card 2: Partido B - Impulso -->
            <article class="card">
                <div class="card-header orange">
                    <h3 class="card-title">Partido B - Impulso</h3>
                    <p class="card-subtitle">"Acción que Transforma"</p>
                </div>
                <div class="card-body">
                    <div class="avatar-group">
                        <div class="avatar"></div>
                        <div class="avatar"></div>
                        <div class="avatar"></div>
                        <div class="avatar avatar-more">+3</div>
                    </div>
                    <p class="card-description">
                        Buscamos transformar incubunt en un referente nacional comprometido con la innovación y el emprendimiento estudiantil de impacto.
                    </p>
                    <a href="#" class="card-link orange">Ver equipo y propuestas →</a>
                </div>
            </article>
        </div>
    </div>
</body>
</html>