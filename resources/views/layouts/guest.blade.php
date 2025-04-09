<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>
        <link href="{{ asset('library/adminlte/css/adminlte.css') }}" rel="stylesheet" />
        
    </head>
    <style>
        .login-logo .main-logo{
    max-width: 140px;
}
    </style>
    <body class="login-page bg-body-secondary">
        <div class="login-box">
            <div class="login-logo">
                <a href="/">
                    <!-- <b>Cold</b>Storage -->
                    <img
                        src="{{ asset('assets/images/brand-logo.png') }}"
                        class="main-logo"
                        alt="Ubrand-logo"
                    />
                </a>
            </div>

            <div class="card">
                <div class="card-body login-card-body">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
