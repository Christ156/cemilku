@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
@endsection

@extends('adminlte::page')

@section('title', 'Data User')

@section('content')
    <div class="content-header">
        <h1>User List</h1>
    </div>

    <x-adminlte-card>

        <x-adminlte-datatable id="userTable" :heads="['No.', 'ID', 'Name', 'Action']" striped hoverable bordered with-buttons>
            @foreach ($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>
                        <x-adminlte-button class="btn-edit" icon="fas fa-edit" size="sm"
                            title="Log" label="Log" />
                        <form action="" method="POST" style="display:inline-block;">
                            @csrf
                            {{-- @method('DELETE') --}}
                            <x-adminlte-button class="btn-delete" icon="fas fa-user-slash" size="sm" title="Block" label="Block" type="submit"/>
                        </form>
                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>

    </x-adminlte-card>

    {{-- Tombol Ekspor --}}
    <a href="{{ route('adminuser.export') }}" class="btn btn-export mb-3">
        Export to Excel
    </a>

    {{-- Pesan Sukses --}}
    @if (session('success'))
        <x-adminlte-alert theme="success" title="Berhasil" class="mt-3">
            {{ session('success') }}
        </x-adminlte-alert>
    @endif
@endsection
