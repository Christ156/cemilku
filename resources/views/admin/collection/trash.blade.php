@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
@endsection

@extends('adminlte::page')

@section('title', 'Trash - Collections')

@section('content')
    <div class="content-header">
        <h1>Trash - Deleted Collections</h1>
    </div>

    <a href="{{ route('admincollection.index') }}" class="btn btn-yellowbrown mb-3">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No.</th>
                <th>Name</th>
                <th>Deleted At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($trashedCollections as $index => $collection)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $collection->name }}</td>
                    <td>{{ $collection->deleted_at }}</td>
                    <td>
                        <form action="{{ route('admincollection.restore', $collection->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('PUT')
                            <button class="btn btn-add btn-sm" onclick="return confirm('Restore collection ini?')">Restore</button>
                        </form>
                        <form action="{{ route('admincollection.force-delete', $collection->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-delete btn-sm" onclick="return confirm('Hapus permanen collection ini?')">Delete Permanently</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada collection yang terhapus.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($trashedCollections->count() > 0)
        <form action="{{ route('admincollection.restore-all') }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin mengembalikan semua collection yang terhapus?')">
            @csrf
            @method('PUT')
            <button type="submit" class="btn btn-export mb-3">
                <i class="fas fa-undo"></i> Restore All
            </button>
        </form>
    @endif
@endsection
