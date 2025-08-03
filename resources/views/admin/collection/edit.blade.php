@extends('adminlte::page')

@section('title', 'Edit Collection')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
@endsection

@section('content')
    <div class="content-header">
        <h1>{{__('adminCollection.editCollection')}}</h1>
    </div>

    <x-adminlte-card>
        <form action="{{ route('admincollection.update', $collection->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Nama Collection --}}
            <x-adminlte-input name="name" label="{{__('adminCollection.collectionName')}}" placeholder="{{__('adminCollection.enterCollectionName')}}"
                value="{{ old('name', $collection->name) }}" required />

            {{-- Jenis Collection --}}
            <x-adminlte-select name="type" label="Type" required>
                <option value="">-- {{__('adminCollection.chooseType')}} --</option>
                <option value="tower" {{ old('type', $collection->type) == 'tower' ? 'selected' : '' }}>{{__('adminCollection.tower')}}</option>
                <option value="bouquet" {{ old('type', $collection->type) == 'bouquet' ? 'selected' : '' }}>{{__('adminCollection.bouquet')}}</option>
            </x-adminlte-select>

            {{-- Kategori --}}
            <x-adminlte-select name="category" label="{{__('adminCollection.category')}}" required>
                @php
                    $categories = ['Chinese New Year', 'Valentine', 'Ramadhan', 'Christmas', 'Birthday', 'Graduation'];
                @endphp
                <option value="">-- {{__('adminCollection.chooseCategory')}} --</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}" {{ old('category', $collection->category) == $cat ? 'selected' : '' }}>
                        {{ $cat }}
                    </option>
                @endforeach
            </x-adminlte-select>

            {{-- Deskripsi --}}
            <x-adminlte-textarea name="description" label="{{__('adminCollection.description')}}" rows="3"
                placeholder="{{__('adminCollection.enterDescriptionOptional')}}">{{ old('description', $collection->description) }}</x-adminlte-textarea>

            {{-- Harga --}}
            <x-adminlte-input name="price" label="{{__('adminCollection.price')}} (Rp)" type="number"
                value="{{ old('price', $collection->price) }}" required />

            {{-- Stok --}}
            <x-adminlte-input name="stock" label="{{__('adminCollection.stock')}}" type="number"
                value="{{ old('stock', $collection->stock) }}" required />

            {{-- Gambar --}}
            <x-adminlte-input name="image" label="{{__('adminCollection.collectionImage')}}" type="file" accept="image/*" />
            @if ($collection->image)
                <p class="mt-1">{{__('adminCollection.current')}}: <img src="{{ asset('assets/collections/' . $collection->image) }}"
                        alt="Image" width="60" height="60" style="object-fit: cover; border-radius: 6px;" />
                </p>
            @endif

            {{-- Snack dan Quantity --}}
            @php
                $snacks = \App\Models\Snack::all();
                $snackLayer = $collection->snacks->pluck('pivot.quantity', 'id'); // [snack_id => quantity]
                $snackIds = $collection->snacks->pluck('id')->values(); // [id1, id2, id3, id4]
            @endphp

            <h5 class="mt-4 mb-2">{{__('adminCollection.snackForEachLayer')}}</h5>

            @for ($i = 1; $i <= 4; $i++)
                @php
                    $selectedSnackId = old("snack_id_$i", $snackIds[$i - 1] ?? null);
                    $selectedQty = old("quantity_$i", $selectedSnackId ? ($snackLayer[$selectedSnackId] ?? '') : '');
                @endphp
                <div class="row">
                    <div class="col-md-6">
                        <x-adminlte-select name="snack_id_{{ $i }}" label="Snack for Layer {{ $i }}" required>
                            <option value="">-- {{__('adminCollection.chooseSnack')}} --</option>
                            @foreach ($snacks as $snack)
                                <option value="{{ $snack->id }}"
                                    {{ $selectedSnackId == $snack->id ? 'selected' : '' }}>
                                    {{ $snack->name }}
                                </option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                </div>
            @endfor

            <div class="mt-3">
                <x-adminlte-button type="submit" theme="primary" icon="fas fa-save" label="{{__('adminCollection.update')}}" />
                <a href="{{ route('admincollection.index') }}" class="btn btn-secondary ml-2">{{__('adminCollection.cancel')}}</a>
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
