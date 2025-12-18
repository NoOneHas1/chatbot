<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Asesor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: system-ui, sans-serif;
            background: #f4f6f8;
            color: #0f3b53;
        }

        .layout {
            display: flex;
            height: 100vh;
        }

        aside {
            width: 240px;
            background: #0f3b53;
            color: white;
            padding: 20px;
        }

        aside h2 {
            font-size: 18px;
            margin-bottom: 30px;
        }

        aside a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px 0;
            opacity: 0.85;
        }

        aside a:hover {
            opacity: 1;
        }

        main {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }
    </style>
</head>
<body>

<div class="layout">
    <aside>
        <h2>Asesor</h2>

        <a href="{{ route('advisor.dashboard') }}">Dashboard</a>
        <a href="{{ route('advisor.request') }}">Solicitudes</a>
    </aside>

    <main>
        @yield('content')
    </main>
</div>

</body>
</html>
