@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
@endsection

@extends('adminlte::page')

@section('title', 'Trash - Snacks')

@section('content')
    <div class="content-header">
        <h1>{{__('adminSnack.trash')}}</h1>
    </div>


    <a href="{{ route('adminsnack.index') }}" class="btn btn-yellowbrown mb-3">
        <i class="fas fa-arrow-left "></i> {{__('adminSnack.backToList')}}
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
            @forelse ($trashedSnacks as $index => $snack)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $snack->name }}</td>
                    <td>{{ $snack->deleted_at }}</td>
                    <td>
                        <form action="{{ route('adminsnack.restore', $snack->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('PUT')
                            <button class="btn btn-add btn-sm" onclick="return confirm('{{__('adminSnack.restoreSnack')}}')">{{__('adminSnack.restore')}}</button>
                        </form>
                        <form action="{{ route('adminsnack.force-delete', $snack->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-delete btn-sm" onclick="return confirm('{{__('adminSnack.deletePermanent')}}')">{{__('adminSnack.deletePermanently')}}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">{{__('adminSnack.noSnackDeleted')}}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <form action="{{ route('adminsnack.restore-all') }}" method="POST" style="display: inline-block;" onsubmit="return confirm('{{__('adminSnack.areYouSureRestore')}}')">
    @csrf
    @method('PUT')
    <button type="submit" class="btn btn-export mb-3">
        <i class="fas fa-undo"></i> {{__('adminSnack.restoreAll')}}
    </button>
    </form>
@endsection
