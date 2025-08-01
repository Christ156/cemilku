@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
@endsection

@extends('adminlte::page')

@section('title', 'Data Snack')

@section('content')
    <div class="content-header">
        <h1>{{__('adminSnack.snackList')}}</h1>
    </div>

    <x-adminlte-card>

        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('adminsnack.create') }}" class="btn btn-add">
                <i class="fas fa-plus"></i> {{__('adminSnack.addSnack')}}
            </a>

            {{-- Tombol ke halaman recycle bin --}}
            <a href="{{ route('adminsnack.trash') }}" class="btn btn-yellowbrown">
                <i class="fas fa-trash-alt"></i> {{__('adminSnack.viewTrash')}}
            </a>
        </div>

        <x-adminlte-datatable id="snackTable" :heads="['No.', 'Image', 'Name', 'Price', 'Stock', 'Action']" striped hoverable bordered with-buttons>
            @foreach ($snacks as $index => $snack)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        @if ($snack->image)
                            <img src="{{ asset('assets/snack_items/' . $snack->image) }}" alt="Snack Image" width="60"
                                height="60" style="object-fit: cover; border-radius: 8px;">
                        @else
                            <span class="text-muted">{{__('adminSnack.noImage')}}</span>
                        @endif
                    </td>
                    <td>{{ $snack->name }}</td>
                    <td>Rp{{ number_format($snack->price, 0, ',', '.') }}</td>
                    <td>{{ $snack->stock }}</td>
                    <td>
                        <x-adminlte-button class="btn-edit" icon="fas fa-edit" size="sm" title="Edit" label="{{__('adminSnack.edit')}}"
                            onclick="location.href='{{ route('adminsnack.edit', $snack->id) }}'" />
                        <form action="{{ route('adminsnack.destroy', $snack->id) }}" method="POST"
                            style="display:inline-block;"
                            onsubmit="return confirm('{{__('adminSnack.areYouSureDelete')}}')">
                            @csrf
                            @method('DELETE')
                            <x-adminlte-button class="btn-delete" icon="fas fa-trash" size="sm" title="Hapus"
                                label="{{__('adminSnack.delete')}}" type="submit" />
                        </form>
                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>

    </x-adminlte-card>

    {{-- Tombol Ekspor --}}
    <a href="{{ route('adminsnack.export') }}" class="btn btn-export mb-3">
        {{__('adminSnack.exportToExcel')}}
    </a>

    {{-- Form Impor --}}
    <form action="{{ route('adminsnack.import') }}" method="POST" enctype="multipart/form-data" class="mt-3">
        @csrf
        <input type="file" name="file" required>
        <button type="submit" class="btn btn-add">{{__('adminSnack.importExcel')}}</button>

        {{-- Menampilkan error jika file tidak sesuai --}}
        @error('file')
            <div class="text-danger mt-2">
                {{ $message }}
            </div>
        @enderror
    </form>

    {{-- Pesan Sukses --}}
    @if (session('success'))
        <x-adminlte-alert theme="success" title="Berhasil" class="mt-3">
            {{ session('success') }}
        </x-adminlte-alert>
    @endif
@endsection
