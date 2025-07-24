@extends('adminlte::page')

@section('title', 'Edit Collection')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
@endsection

@section('content')
    <div class="content-header">
        <h1>Edit Collection</h1>
    </div>

    <x-adminlte-card>
        <form action="{{ route('admincollection.update', $collection->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Nama Collection --}}
            <x-adminlte-input name="name" label="Collection Name" placeholder="Enter collection name"
                value="{{ old('name', $collection->name) }}" required />

            {{-- Jenis Collection --}}
            <x-adminlte-select name="type" label="Type" required>
                <option value="">-- Choose Type --</option>
                <option value="tower" {{ old('type', $collection->type) == 'tower' ? 'selected' : '' }}>Tower</option>
                <option value="bouquet" {{ old('type', $collection->type) == 'bouquet' ? 'selected' : '' }}>Bouquet</option>
            </x-adminlte-select>

            {{-- Kategori --}}
            <x-adminlte-select name="category" label="Category" required>
                @php
                    $categories = ['Chinese New Year', 'Valentine', 'Ramadhan', 'Christmas', 'Birthday', 'Graduation'];
                @endphp
                <option value="">-- Choose Category --</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}" {{ old('category', $collection->category) == $cat ? 'selected' : '' }}>
                        {{ $cat }}
                    </option>
                @endforeach
            </x-adminlte-select>

            {{-- Deskripsi --}}
            <x-adminlte-textarea name="description" label="Description" rows="3"
                placeholder="Enter description (optional)">{{ old('description', $collection->description) }}</x-adminlte-textarea>

            {{-- Harga --}}
            <x-adminlte-input name="price" label="Price (Rp)" type="number"
                value="{{ old('price', $collection->price) }}" required />

            {{-- Stok --}}
            <x-adminlte-input name="stock" label="Stock" type="number"
                value="{{ old('stock', $collection->stock) }}" required />

            {{-- Gambar --}}
            <x-adminlte-input name="image" label="Collection Image" type="file" accept="image/*" />
            @if ($collection->image)
                <p class="mt-1">Current: <img src="{{ asset('assets/collections/' . $collection->image) }}"
                        alt="Image" width="60" height="60" style="object-fit: cover; border-radius: 6px;" />
                </p>
            @endif

            {{-- Snack dan Quantity --}}
            @php
                $snacks = \App\Models\Snack::all();
                $snackLayer = $collection->snacks->pluck('pivot.quantity', 'id'); // [snack_id => quantity]
                $snackIds = $collection->snacks->pluck('id')->values(); // [id1, id2, id3, id4]
            @endphp

            <h5 class="mt-4 mb-2">Snacks for Each Layer (4 Layers)</h5>

            @for ($i = 1; $i <= 4; $i++)
                @php
                    $selectedSnackId = old("snack_id_$i", $snackIds[$i - 1] ?? null);
                    $selectedQty = old("quantity_$i", $selectedSnackId ? ($snackLayer[$selectedSnackId] ?? '') : '');
                @endphp
                <div class="row">
                    <div class="col-md-6">
                        <x-adminlte-select name="snack_id_{{ $i }}" label="Snack for Layer {{ $i }}" required>
                            <option value="">-- Choose Snack --</option>
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
                <x-adminlte-button type="submit" theme="primary" icon="fas fa-save" label="Update" />
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
