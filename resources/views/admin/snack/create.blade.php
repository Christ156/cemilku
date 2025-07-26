@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
@endsection

@extends('adminlte::page')

@section('title', 'Add Snack')

@section('content')
    <div class="content-header">
        <h1>Add Snack</h1>
    </div>

    <x-adminlte-card>
        {{-- Global error summary (opsional tapi membantu) --}}
        @if ($errors->any())
            <x-adminlte-alert theme="danger" title="Validation Error">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </x-adminlte-alert>
        @endif

        <form action="{{ route('adminsnack.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Name --}}
            <div class="form-group">
                <label for="name">Snack Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                    class="form-control @error('name') is-invalid @enderror" placeholder="Enter snack name" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Price --}}
            <div class="form-group">
                <label for="price">Price (Rp)</label>
                <input type="number" id="price" name="price" value="{{ old('price') }}"
                    class="form-control @error('price') is-invalid @enderror" required>
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Stock --}}
            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="number" id="stock" name="stock" value="{{ old('stock') }}"
                    class="form-control @error('stock') is-invalid @enderror" required>
                @error('stock')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Image --}}
            <div class="form-group">
                <label for="image">Snack Image</label>
                <input type="file" id="image" name="image"
                    class="form-control-file @error('image') is-invalid @enderror" accept="image/*">
                @error('image')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <x-adminlte-button type="submit" theme="primary" icon="fas fa-plus" label="Add" />
            <a href="{{ route('adminsnack.index') }}" class="btn btn-secondary ml-2">Cancel</a>
        </form>
    </x-adminlte-card>

@endsection
