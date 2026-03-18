<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Document légal') — Ultra</title>
    @include('partials.head-assets')
    <style>
        body.legal-page { font-family: system-ui, -apple-system, sans-serif; background: #0f172a; color: #e2e8f0; min-height: 100vh; }
        .legal-page a { color: #38bdf8; text-decoration: none; }
        .legal-page a:hover { text-decoration: underline; }
    </style>
</head>
<body class="legal-page">
    <div class="min-h-screen py-8 px-4">
        @yield('content')
    </div>
</body>
</html>
