@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
@endsection

@extends('adminlte::page')

@section('title', 'Trash - Decorations')

@section('content')
    <div class="content-header">
        <h1>{{__('adminDecoration.trashDeleted')}}</h1>
    </div>

    <a href="{{ route('admindecoration.index') }}" class="btn btn-yellowbrown mb-3">
        <i class="fas fa-arrow-left "></i> {{__('adminDecoration.backToList')}}
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
            @forelse ($trashedDecorations as $index => $decoration)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $decoration->name }}</td>
                    <td>{{ $decoration->deleted_at }}</td>
                    <td>
                        <form action="{{ route('admindecoration.restore', $decoration->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('PUT')
                            <button class="btn btn-add btn-sm" onclick="return confirm('{{__('adminDecoration.restoreThis')}}')">{{__('adminDecoration.restore')}}</button>
                        </form>
                        <form action="{{ route('admindecoration.force-delete', $decoration->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-delete btn-sm" onclick="return confirm('{{__('adminDecoration.deletePermanent')}}')">{{__('adminDecoration.deletePermanently')}}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">{{__('adminDecoration.noDecorationDeleted')}}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <form action="{{ route('admindecoration.restore-all') }}" method="POST" style="display: inline-block;" onsubmit="return confirm('{{__('adminDecoration.areYouSureRestore')}}')">
        @csrf
        @method('PUT')
        <button type="submit" class="btn btn-export mb-3">
            <i class="fas fa-undo"></i> {{__('adminDecoration.restoreAll')}}
        </button>
    </form>
@endsection
