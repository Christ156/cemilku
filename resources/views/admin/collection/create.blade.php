@extends('adminlte::page')

@section('title', 'Add Collection')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
@endsection

@section('content')
    <div class="content-header">
        <h1>{{ __('adminCollection.addCollection') }}</h1>
    </div>

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

    <x-adminlte-card>
        <form action="{{ route('admincollection.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Nama Collection --}}
            <x-adminlte-input name="name" label="{{__('adminCollection.collectionName')}}" placeholder="{{__('adminCollection.enterCollectionName')}}" required
                value="{{ old('name') }}" />

            {{-- Kategori --}}
            <x-adminlte-select name="category" label="{{__('adminCollection.category')}}" required>
                <option value="">-- {{__('adminCollection.chooseCategory')}} --</option>
                @foreach (['Chinese New Year', 'Valentine', 'Ramadhan', 'Christmas', 'Birthday', 'Graduation'] as $cat)
                    <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </x-adminlte-select>

            {{-- Jenis Collection --}}
            <x-adminlte-select name="type" label="{{__('adminCollection.type')}}" required>
                <option value="">-- {{__('adminCollection.chooseType')}} --</option>
                <option value="tower" {{ old('type') == 'tower' ? 'selected' : '' }}>{{__('adminCollection.tower')}}</option>
                <option value="bouquet" {{ old('type') == 'bouquet' ? 'selected' : '' }}>{{__('adminCollection.bouquet')}}</option>
            </x-adminlte-select>

            {{-- Deskripsi --}}
            <x-adminlte-textarea name="description" label="{{__('adminCollection.description')}}" rows="3" placeholder="{{__('adminCollection.enterDescription')}}">
                {{ old('description') }}
            </x-adminlte-textarea>

            {{-- Harga --}}
            <x-adminlte-input name="price" label="{{__('adminCollection.price')}} (Rp)" type="number" required value="{{ old('price') }}" />

            {{-- Stok --}}
            <x-adminlte-input name="stock" label="{{__('adminCollection.stock')}}" type="number" required value="{{ old('stock') }}" />

            {{-- Gambar --}}
            <x-adminlte-input name="image" label="{{__('adminCollection.collectionImage')}}" type="file" accept="image/*" />

            {{-- Snack per Layer --}}
            @php
                $snacks = \App\Models\Snack::all();
            @endphp

            <h5 class="mt-4 mb-2">{{__('adminCollection.snackForEachLayer')}}</h5>

            @for ($i = 1; $i <= 4; $i++)
                <div class="row">
                    <div class="col-md-6">
                        <x-adminlte-select name="snack_id_{{ $i }}" label="Snack for Layer {{ $i }}" required>
                            <option value="">-- {{__('adminCollection.chooseSnack')}} --</option>
                            @foreach ($snacks as $snack)
                                <option value="{{ $snack->id }}"
                                    {{ old("snack_id_$i") == $snack->id ? 'selected' : '' }}>
                                    {{ $snack->name }}
                                </option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                </div>
            @endfor

            <div class="mt-3">
                <x-adminlte-button type="submit" theme="primary" icon="fas fa-plus" label="Add" />
                <a href="{{ route('admincollection.index') }}" class="btn btn-secondary ml-2">{{__('adminCollection.cancel')}}</a>
            </div>
        </form>

    </x-adminlte-card>
@endsection
