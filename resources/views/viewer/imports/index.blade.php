@extends('layouts.viewer')

@section('title', 'Imports')

@section('content')
    <h1>Imports</h1>

    @if ($imports->isEmpty())
        <p class="empty">No imports found.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Supplier</th>
                    <th>External import ID</th>
                    <th>Sent at</th>
                    <th>Status</th>
                    <th>Total offers</th>
                    <th>Processed offers</th>
                    <th>Created at</th>
                    <th>Completed at</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($imports as $import)
                    <tr>
                        <td>{{ $import->id }}</td>
                        <td>{{ $import->supplier?->name }}</td>
                        <td>{{ $import->external_import_id }}</td>
                        <td>{{ $import->sent_at?->format('Y-m-d H:i') }}</td>
                        <td><span class="badge badge-{{ $import->status }}">{{ $import->status }}</span></td>
                        <td>{{ $import->total_offers }}</td>
                        <td>{{ $import->processed_offers }}</td>
                        <td>{{ $import->created_at?->format('Y-m-d H:i') }}</td>
                        <td>{{ $import->completed_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            @if ($imports->onFirstPage())
                <span class="disabled">&larr; Previous</span>
            @else
                <a href="{{ $imports->previousPageUrl() }}">&larr; Previous</a>
            @endif

            <span>Page {{ $imports->currentPage() }} of {{ $imports->lastPage() }}</span>

            @if ($imports->hasMorePages())
                <a href="{{ $imports->nextPageUrl() }}">Next &rarr;</a>
            @else
                <span class="disabled">Next &rarr;</span>
            @endif
        </div>
    @endif
@endsection
