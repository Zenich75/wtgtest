@extends('layouts.viewer')

@section('title', 'Offers')

@section('content')
    <h1>Offers</h1>

    <form class="filters" method="GET" action="{{ route('viewer.offers.index') }}">
        <input type="text" name="supplier" placeholder="Supplier code" value="{{ request('supplier') }}">
        <input type="text" name="city" placeholder="City" value="{{ request('city') }}">
        <button type="submit">Filter</button>
        @if (request('supplier') || request('city'))
            <a href="{{ route('viewer.offers.index') }}">Clear</a>
        @endif
    </form>

    @if ($offers->isEmpty())
        <p class="empty">No offers found.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Supplier</th>
                    <th>Property code</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Price</th>
                    <th>Currency</th>
                    <th>Available units</th>
                    <th>Expires at</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($offers as $offer)
                    <tr>
                        <td>{{ $offer->id }}</td>
                        <td>{{ $offer->supplier?->name }}</td>
                        <td>{{ $offer->property?->code }}</td>
                        <td>{{ $offer->check_in?->format('Y-m-d') }}</td>
                        <td>{{ $offer->check_out?->format('Y-m-d') }}</td>
                        <td>{{ $offer->price }}</td>
                        <td>{{ $offer->currency }}</td>
                        <td>{{ $offer->available_units }}</td>
                        <td>{{ $offer->expires_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            @if ($offers->onFirstPage())
                <span class="disabled">&larr; Previous</span>
            @else
                <a href="{{ $offers->previousPageUrl() }}">&larr; Previous</a>
            @endif

            <span>Page {{ $offers->currentPage() }} of {{ $offers->lastPage() }}</span>

            @if ($offers->hasMorePages())
                <a href="{{ $offers->nextPageUrl() }}">Next &rarr;</a>
            @else
                <span class="disabled">Next &rarr;</span>
            @endif
        </div>
    @endif
@endsection
