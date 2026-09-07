<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'WTG Spain API') }}</title>

        <style>
            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
                background: #f8f8f7;
                color: #1b1b18;
            }

            main {
                max-width: 640px;
                width: 100%;
                margin: 2rem;
                padding: 2.5rem;
                background: #fff;
                border: 1px solid #e3e3e0;
                border-radius: 0.5rem;
            }

            h1 {
                font-size: 1.5rem;
                margin: 0 0 1rem;
            }

            p {
                line-height: 1.6;
                color: #4b4b47;
                margin: 0 0 1.5rem;
            }

            h2 {
                font-size: 1rem;
                text-transform: uppercase;
                letter-spacing: 0.03em;
                color: #706f6c;
                margin: 0 0 0.75rem;
            }

            ul {
                list-style: none;
                margin: 0;
                padding: 0;
            }

            li {
                padding: 0.6rem 0;
                border-top: 1px solid #eeeeec;
            }

            li:first-child {
                border-top: none;
            }

            a {
                color: #f53003;
                text-decoration: none;
                font-weight: 500;
            }

            a:hover {
                text-decoration: underline;
            }

            code {
                background: #f2f2f0;
                padding: 0.15rem 0.4rem;
                border-radius: 0.25rem;
                font-size: 0.9em;
            }

            @media (prefers-color-scheme: dark) {
                body {
                    background: #0a0a0a;
                    color: #ededec;
                }

                main {
                    background: #161615;
                    border-color: #3e3e3a;
                }

                p {
                    color: #a1a09a;
                }

                h2 {
                    color: #a1a09a;
                }

                li {
                    border-top-color: #3e3e3a;
                }

                code {
                    background: #1f1f1e;
                }
            }
        </style>
    </head>
    <body>
        <main>
            <h1>WTG Spain API</h1>
            <p>
                A REST API for asynchronously importing housing offers from suppliers, searching
                properties for the cheapest available offer, and booking offers through
                concurrency-safe reservations.
            </p>

            <h2>Resources</h2>
            <ul>
                <li>
                    <a href="/api/documentation">Swagger API documentation</a> &mdash; browse and try the full API reference.
                </li>
                <li>
                    Postman collection &mdash; import <code>docs/postman_collection.json</code> into Postman to explore the API from your own client.
                </li>
                <li>
                    <a href="{{ route('viewer.imports.index') }}">Browse data</a> &mdash; internal read-only tables for imports, properties, offers, and reservations.
                </li>
            </ul>
        </main>
    </body>
</html>
