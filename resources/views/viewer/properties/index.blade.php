@extends('layouts.viewer')

@section('title', 'Properties')

@section('content')
    <h1>Properties</h1>

    @if ($properties->isEmpty())
        <p class="empty">No properties found.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>City</th>
                    <th>Offers</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($properties as $property)
                    <tr>
                        <td>{{ $property->code }}</td>
                        <td>{{ $property->name }}</td>
                        <td>{{ $property->city }}</td>
                        <td>{{ $property->offers_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            @if ($properties->onFirstPage())
                <span class="disabled">&larr; Previous</span>
            @else
                <a href="{{ $properties->previousPageUrl() }}">&larr; Previous</a>
            @endif

            <span>Page {{ $properties->currentPage() }} of {{ $properties->lastPage() }}</span>

            @if ($properties->hasMorePages())
                <a href="{{ $properties->nextPageUrl() }}">Next &rarr;</a>
            @else
                <span class="disabled">Next &rarr;</span>
            @endif
        </div>
    @endif
@endsection
