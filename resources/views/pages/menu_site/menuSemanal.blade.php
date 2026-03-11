<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menú Semanal · Soda IACSA</title>
    <meta name="description" content="Menú semanal de la Soda ETAI — Desayuno, Almuerzo y Refrigerio">

    <link rel="icon" type="image/png" href="{{ asset('site/img/breakfast.png') }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Estilos -->
    <link rel="stylesheet" href="{{ asset('site/css/style.css') }}">
</head>

<body>

    {{-- ── LOADER ─────────────────────────────────────────────────────── --}}
    <div id="page-loader">
        <img src="{{ asset('site/img/menu.gif') }}" alt="Cargando...">
        <p>Preparando el menú...</p>
    </div>

    {{-- ── NAVBAR ─────────────────────────────────────────────────────── --}}
    <nav class="main-nav">
        <div class="container">
            <div class="nav-inner">
                <img src="{{ asset('build/assets/images/Logo.png') }}" alt="Logo ETAI" class="nav-logo">
                <div>
                    <div class="nav-brand-title">Soda IACSA</div>
                    <div class="nav-brand-sub">Instituto Agropecuario Costarricense</div>
                </div>
            </div>
        </div>
    </nav>

    {{-- ── BANNER SEMANA ───────────────────────────────────────────────── --}}
    <div class="week-banner">
        <div class="container">
            <h1><i class="fas fa-utensils me-2"></i>Menú de la Semana</h1>
            <p class="week-dates">
                <i class="fas fa-calendar-week"></i>{{ $fechaSemana }}
            </p>
        </div>
    </div>

    {{-- ── GRID SEMANAL ────────────────────────────────────────────────── --}}
    <section class="week-section">
        <div class="container">

            @if(!$hoy)
            {{-- Fin de semana --}}
            <div class="weekend-notice">
                <div class="emoji"><i class="fas fa-umbrella-beach fa-3x icon-weekend"></i></div>
                <p class="fw-semibold mt-2">¡Es fin de semana!</p>
                <p>El menú estará disponible nuevamente el lunes. ¡Que descansen!</p>
            </div>
            @endif

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-3">

                @foreach($semana as $dia => $comidas)
                <div class="col">
                    <div class="day-card {{ $dia === $hoy ? 'today' : '' }}">

                        {{-- Encabezado día --}}
                        <div class="day-header">
                            <span class="day-name">{{ $dia }}</span>
                            @if($dia === $hoy)
                                <span class="hoy-badge">Hoy</span>
                            @endif
                        </div>

                        {{-- Desayuno --}}
                        <div class="meal-item">
                            <span class="meal-emoji"><i class="fas fa-mug-hot icon-desayuno"></i></span>
                            <div>
                                <div class="meal-type">Desayuno</div>
                                <div class="meal-name">{{ $comidas['desayuno'] }}</div>
                            </div>
                        </div>

                        {{-- Almuerzo --}}
                        <div class="meal-item">
                            <span class="meal-emoji"><i class="fas fa-utensils icon-almuerzo"></i></span>
                            <div>
                                <div class="meal-type">Almuerzo</div>
                                <div class="meal-name">{{ $comidas['almuerzo'] }}</div>
                            </div>
                        </div>

                        {{-- Refrigerio --}}
                        <div class="meal-item">
                            <span class="meal-emoji"><i class="fas fa-apple-whole icon-refrigerio"></i></span>
                            <div>
                                <div class="meal-type">Refrigerio</div>
                                <div class="meal-name">{{ $comidas['refrigerio'] }}</div>
                            </div>
                        </div>

                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </section>

    {{-- ── FOOTER ──────────────────────────────────────────────────────── --}}
    <footer class="main-footer">
        <div class="container">
            <p class="footer-brand">Soda · IACSA</p>
            <p>¡Buen provecho! &nbsp;<i class="fas fa-face-smile"></i></p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('site/js/menuSemanal.js') }}"></script>

</body>
</html>
