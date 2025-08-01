@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
@endsection

@extends('adminlte::page')

@section('title', 'Edit Snack')

@section('content')
    <div class="content-header">
        <h1>{{__('adminSnack.editSnack')}}</h1>
    </div>

    <x-adminlte-card>
        <form action="{{ route('adminsnack.update', $snack->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-adminlte-input name="name" label="{{__('adminSnack.snackName')}}" value="{{ $snack->name }}" required />
            <x-adminlte-input name="price" label="{{__('adminSnack.price')}} (Rp)" type="number" value="{{ $snack->price }}" required />
            <x-adminlte-input name="stock" label="{{__('adminSnack.stock')}}" type="number" value="{{ $snack->stock }}" required />

            {{-- Tampilkan gambar saat ini --}}
            @if ($snack->image)
                <div class="mb-3">
                    <label>{{__('adminSnack.currentImage')}}:</label><br>
                    <img src="{{ asset('assets/snack_items/' . $snack->image) }}" alt="Snack Image" width="120"
                        height="120" style="object-fit: cover; border-radius: 8px;">
                </div>
            @endif

            {{-- Input untuk ganti gambar --}}
            <div class="mb-3">
                <label for="image">{{__('adminSnack.changeImageOptional')}}:</label>
                <input type="file" name="image" id="image" class="form-control-file">
                @error('image')
                    <span class="text-danger small d-block mt-1">{{ $message }}</span>
                @enderror
            </div>


            <x-adminlte-button type="submit" theme="success" icon="fas fa-save" label="{{__('adminSnack.update')}}" />
            <a href="{{ route('adminsnack.index') }}" class="btn btn-secondary ml-2">{{__('adminSnack.cancel')}}</a>
        </form>
    </x-adminlte-card>
@endsection
