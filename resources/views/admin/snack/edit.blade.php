@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
@endsection

@extends('adminlte::page')

@section('title', 'Edit Snack')

@section('content')
    <div class="content-header">
        <h1>Edit Snack</h1>
    </div>

    <x-adminlte-card>
        <form action="{{ route('adminsnack.update', $snack->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-adminlte-input name="name" label="Snack Name" value="{{ $snack->name }}" required />
            <x-adminlte-input name="price" label="Price (Rp)" type="number" value="{{ $snack->price }}" required />
            <x-adminlte-input name="stock" label="Stock" type="number" value="{{ $snack->stock }}" required />

            {{-- Tampilkan gambar saat ini --}}
            @if ($snack->image)
                <div class="mb-3">
                    <label>Current Image:</label><br>
                    <img src="{{ asset('assets/snack_items/' . $snack->image) }}" alt="Snack Image" width="120"
                        height="120" style="object-fit: cover; border-radius: 8px;">
                </div>
            @endif

            {{-- Input untuk ganti gambar --}}
            <div class="mb-3">
                <label for="image">Change Image (optional):</label>
                <input type="file" name="image" id="image" class="form-control-file">
                @error('image')
                    <span class="text-danger small d-block mt-1">{{ $message }}</span>
                @enderror
            </div>


            <x-adminlte-button type="submit" theme="success" icon="fas fa-save" label="Update" />
            <a href="{{ route('adminsnack.index') }}" class="btn btn-secondary ml-2">Cancel</a>
        </form>
    </x-adminlte-card>
@endsection
