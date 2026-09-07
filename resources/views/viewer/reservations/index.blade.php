@extends('layouts.viewer')

@section('title', 'Reservations')

@section('content')
    <h1>Reservations</h1>

    @if ($reservations->isEmpty())
        <p class="empty">No reservations found.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Offer ID</th>
                    <th>Client reference</th>
                    <th>Customer name</th>
                    <th>Customer email</th>
                    <th>Status</th>
                    <th>Created at</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reservations as $reservation)
                    <tr>
                        <td>{{ $reservation->id }}</td>
                        <td>{{ $reservation->offer_id }}</td>
                        <td>{{ $reservation->client_reference }}</td>
                        <td>{{ $reservation->customer_name }}</td>
                        <td>{{ $reservation->customer_email }}</td>
                        <td><span class="badge badge-{{ $reservation->status }}">{{ $reservation->status }}</span></td>
                        <td>{{ $reservation->created_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            @if ($reservations->onFirstPage())
                <span class="disabled">&larr; Previous</span>
            @else
                <a href="{{ $reservations->previousPageUrl() }}">&larr; Previous</a>
            @endif

            <span>Page {{ $reservations->currentPage() }} of {{ $reservations->lastPage() }}</span>

            @if ($reservations->hasMorePages())
                <a href="{{ $reservations->nextPageUrl() }}">Next &rarr;</a>
            @else
                <span class="disabled">Next &rarr;</span>
            @endif
        </div>
    @endif
@endsection
