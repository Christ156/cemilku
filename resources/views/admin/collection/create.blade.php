@extends('adminlte::page')

@section('title', 'Add Collection')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
@endsection

@section('content')
    <div class="content-header">
        <h1>Add Collection</h1>
    </div>

    <x-adminlte-card>
        <form action="{{ route('admincollection.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Nama Collection --}}
            <x-adminlte-input name="name" label="Collection Name" placeholder="Enter collection name" required />

            {{-- Kategori --}}
            <x-adminlte-select name="category" label="Category" required>
                <option value="">-- Choose Category --</option>
                <option value="Chinese New Year">Chinese New Year</option>
                <option value="Valentine">Valentine</option>
                <option value="Ramadhan">Ramadhan</option>
                <option value="Christmas">Christmas</option>
                <option value="Birthday">Birthday</option>
                <option value="Graduation">Graduation</option>
            </x-adminlte-select>

            {{-- Jenis Collection --}}
            <x-adminlte-select name="type" label="Type" required>
                <option value="">-- Choose Type --</option>
                <option value="tower">Tower</option>
                <option value="bouquet">Bouquet</option>
            </x-adminlte-select>

            {{-- Deskripsi --}}
            <x-adminlte-textarea name="description" label="Description" rows="3"
                placeholder="Enter description (optional)" />

            {{-- Harga --}}
            <x-adminlte-input name="price" label="Price (Rp)" type="number" required />

            {{-- Stok --}}
            <x-adminlte-input name="stock" label="Stock" type="number" required />

            {{-- Gambar --}}
            <x-adminlte-input name="image" label="Collection Image" type="file" accept="image/*" />

            {{-- Snack per Layer --}}
            @php
                $snacks = \App\Models\Snack::all();
            @endphp

            <h5 class="mt-4 mb-2">Snacks for Each Layer (4 Layers)</h5>

            @for ($i = 1; $i <= 4; $i++)
                <div class="row">
                    <div class="col-md-6">
                        <x-adminlte-select name="snack_id_{{ $i }}" label="Snack for Layer {{ $i }}"
                            required>
                            <option value="">-- Choose Snack --</option>
                            @foreach ($snacks as $snack)
                                <option value="{{ $snack->id }}">{{ $snack->name }}</option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                </div>
            @endfor

            <div class="mt-3">
                <x-adminlte-button type="submit" theme="primary" icon="fas fa-plus" label="Add" />
                <a href="{{ route('admincollection.index') }}" class="btn btn-secondary ml-2">Cancel</a>
            </div>
        </form>
    </x-adminlte-card>

    {{-- Pesan Error --}}
    @if ($errors->any())
        <x-adminlte-alert theme="danger" title="Validation Error" class="mt-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-adminlte-alert>
    @endif
@endsection
