@extends('adminlte::page')

@section('title', 'Data User')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
@endsection

@section('content')
    <div class="content-header">
        <h1>{{__('adminUser.userList')}}</h1>
    </div>

    <x-adminlte-card>

        <x-adminlte-datatable id="userTable" :heads="['No.', 'ID', 'User Name', 'Role', 'Status', 'Action']" striped hoverable bordered with-buttons>
            @foreach ($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>

                    {{-- ROLE --}}
                    <td>
                        @if ($user->role === 'admin')
                            <span class="badge bg-primary text-uppercase">{{__('adminUser.admin')}}</span>
                        @else
                            <span class="badge bg-secondary text-uppercase">{{__('adminUser.user')}}</span>
                        @endif
                    </td>

                    {{-- STATUS --}}
                    <td>
                        @if ($user->is_blocked)
                            <span class="badge bg-danger">{{__('adminUser.blocked')}}</span>
                        @else
                            <span class="badge bg-success">{{__('adminUser.active')}}</span>
                        @endif
                    </td>

                    {{-- ACTION --}}
                    <td>
                        <x-adminlte-button class="btn-edit" icon="fas fa-edit" size="sm" title="Log" label="{{__('adminUser.log')}}" />

                        <form action="{{ route('adminuser.block', $user->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <x-adminlte-button class="btn-delete" icon="fas fa-user-slash" size="sm"
                                title="{{ $user->is_blocked ? 'Unblock' : 'Block' }}"
                                label="{{ $user->is_blocked ? 'Unblock' : 'Block' }}" type="submit"
                                onclick="return confirm('{{__('adminUser.areYouSure')}} {{ $user->is_blocked ? 'Unblock' : 'Block' }} {{__('adminUser.thisUser')}}')" />
                        </form>
                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>

    </x-adminlte-card>

    {{-- Tombol Ekspor --}}
    <a href="{{ route('adminuser.export') }}" class="btn btn-export mb-3">
        {{__('adminUser.exportToExcel')}}
    </a>

    {{-- Pesan Sukses --}}
    @if (session('success'))
        <x-adminlte-alert theme="success" title="Berhasil" class="mt-3">
            {{ session('success') }}
        </x-adminlte-alert>
    @endif
@endsection
