<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'Data browser') &mdash; {{ config('app.name', 'WTG Spain API') }}</title>

        <style>
            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
                background: #f8f8f7;
                color: #1b1b18;
            }

            nav {
                display: flex;
                align-items: center;
                gap: 1.5rem;
                padding: 1rem 2rem;
                background: #fff;
                border-bottom: 1px solid #e3e3e0;
            }

            nav a {
                color: #1b1b18;
                text-decoration: none;
                font-weight: 500;
                font-size: 0.95rem;
            }

            nav a.active {
                color: #f53003;
            }

            nav a:hover {
                text-decoration: underline;
            }

            nav .brand {
                font-weight: 700;
                margin-right: 1rem;
            }

            nav .brand a {
                color: inherit;
            }

            main {
                max-width: 1100px;
                margin: 2rem auto;
                padding: 0 2rem;
            }

            h1 {
                font-size: 1.4rem;
                margin: 0 0 1.5rem;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                background: #fff;
                border: 1px solid #e3e3e0;
                border-radius: 0.5rem;
                overflow: hidden;
            }

            th, td {
                text-align: left;
                padding: 0.6rem 0.9rem;
                border-top: 1px solid #eeeeec;
                font-size: 0.9rem;
            }

            thead th {
                border-top: none;
                background: #f2f2f0;
                text-transform: uppercase;
                letter-spacing: 0.03em;
                font-size: 0.75rem;
                color: #706f6c;
            }

            tbody tr:hover {
                background: #f9f9f8;
            }

            .badge {
                display: inline-block;
                padding: 0.15rem 0.55rem;
                border-radius: 1rem;
                font-size: 0.78rem;
                font-weight: 600;
                text-transform: capitalize;
            }

            .badge-pending {
                background: #fdf1d6;
                color: #8a6116;
            }

            .badge-processing {
                background: #dceafe;
                color: #1b53c9;
            }

            .badge-completed, .badge-confirmed {
                background: #ddf3e4;
                color: #1c7c3d;
            }

            .badge-failed, .badge-cancelled {
                background: #fbdcdb;
                color: #b3261e;
            }

            .filters {
                margin-bottom: 1rem;
                display: flex;
                gap: 0.75rem;
                align-items: center;
            }

            .filters input {
                padding: 0.4rem 0.6rem;
                border: 1px solid #e3e3e0;
                border-radius: 0.35rem;
                font-size: 0.9rem;
            }

            .filters button {
                padding: 0.4rem 0.9rem;
                border: 1px solid #e3e3e0;
                border-radius: 0.35rem;
                background: #fff;
                cursor: pointer;
                font-size: 0.9rem;
            }

            .pagination {
                margin-top: 1.25rem;
                display: flex;
                gap: 1rem;
                align-items: center;
                font-size: 0.9rem;
            }

            .pagination a {
                color: #f53003;
                text-decoration: none;
                font-weight: 500;
            }

            .pagination a:hover {
                text-decoration: underline;
            }

            .pagination .disabled {
                color: #a1a09a;
            }

            .empty {
                color: #706f6c;
                padding: 1.5rem 0;
            }

            @media (prefers-color-scheme: dark) {
                body {
                    background: #0a0a0a;
                    color: #ededec;
                }

                nav {
                    background: #161615;
                    border-color: #3e3e3a;
                }

                nav a {
                    color: #ededec;
                }

                table {
                    background: #161615;
                    border-color: #3e3e3a;
                }

                th, td {
                    border-top-color: #3e3e3a;
                }

                thead th {
                    background: #1f1f1e;
                    color: #a1a09a;
                }

                tbody tr:hover {
                    background: #1a1a19;
                }

                .filters input, .filters button {
                    background: #161615;
                    border-color: #3e3e3a;
                    color: #ededec;
                }

                .empty, .pagination .disabled {
                    color: #a1a09a;
                }
            }
        </style>
    </head>
    <body>
        <nav>
            <span class="brand"><a href="{{ route('viewer.imports.index') }}">Data browser</a></span>
            <a href="{{ route('viewer.imports.index') }}" class="{{ request()->routeIs('viewer.imports.*') ? 'active' : '' }}">Imports</a>
            <a href="{{ route('viewer.properties.index') }}" class="{{ request()->routeIs('viewer.properties.*') ? 'active' : '' }}">Properties</a>
            <a href="{{ route('viewer.offers.index') }}" class="{{ request()->routeIs('viewer.offers.*') ? 'active' : '' }}">Offers</a>
            <a href="{{ route('viewer.reservations.index') }}" class="{{ request()->routeIs('viewer.reservations.*') ? 'active' : '' }}">Reservations</a>
        </nav>

        <main>
            @yield('content')
        </main>
    </body>
</html>
