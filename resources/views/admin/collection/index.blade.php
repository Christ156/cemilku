@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
@endsection

@extends('adminlte::page')

@section('title', 'Data Collection')

@section('content')
    <div class="content-header">
        <h1>{{__('admincollection.collectionList')}}</h1>
    </div>

    <x-adminlte-card>

        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('admincollection.create') }}" class="btn btn-add">
                <i class="fas fa-plus"></i> {{__('adminCollection.addCollection')}}
            </a>

            <a href="{{ route('admincollection.trash') }}" class="btn btn-yellowbrown">
                <i class="fas fa-trash-alt"></i> {{__('adminCollection.viewTrash')}}
            </a>
        </div>

        <x-adminlte-datatable id="collectionTable" :heads="['No.', 'Image', 'Name', 'Type', 'Category', 'Snacks', 'Price', 'Stock', 'Action']" striped hoverable bordered with-buttons>

            @foreach ($collections as $index => $collection)
                <tr>
                    <td>{{ $index + 1 }}</td>

                    {{-- Gambar --}}
                    <td>
                        @if ($collection->image)
                            <img src="{{ asset('assets/collections/' . $collection->image) }}" alt="collection image"
                                width="60" height="60" style="object-fit: cover; border-radius: 6px;">
                        @else
                            <span class="text-muted">{{__('adminCollection.noImage')}}</span>
                        @endif
                    </td>

                    {{-- Nama --}}
                    <td>{{ $collection->name }}</td>

                    <td>{{ ucfirst($collection->type) }}</td>
                    <td>{{ $collection->category }}</td>
                    <td>
                        <ul class="pl-3 mb-0">
                            @foreach ($collection->snacks as $snack)
                                <li>{{ $snack->name }} x {{ $snack->pivot->quantity }}</li>
                            @endforeach
                        </ul>
                    </td>

                    <td>Rp{{ number_format($collection->price, 0, ',', '.') }}</td>
                    <td>{{ $collection->stock }}</td>
                    <td>
                        <x-adminlte-button class="btn-edit" icon="fas fa-edit" size="sm" title="Edit" label="{{__('adminCollection.edit')}}"
                            onclick="location.href='{{ route('admincollection.edit', $collection->id) }}'" />
                        <form action="{{ route('admincollection.destroy', $collection->id) }}" method="POST"
                            style="display:inline-block;" onsubmit="return confirm(__('adminCollectionAreYouSureYouWant'))">
                            @csrf
                            @method('DELETE')
                            <x-adminlte-button class="btn-delete" icon="fas fa-trash" size="sm" title="Hapus"
                                label="{{__('adminCollection.delete')}}" type="submit" />
                        </form>
                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>

    </x-adminlte-card>

    {{-- Tombol Ekspor --}}
    <a href="{{ route('admincollection.export') }}" class="btn btn-export mb-3">
        {{__('adminCollection.exportToExcel')}}
    </a>

    {{-- Form Impor --}}
    <form action="{{ route('admincollection.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit" class="btn btn-add">{{__('adminCollection.importExcel')}}</button>
    </form>

    {{-- Pesan Sukses --}}
    @if (session('success'))
        <x-adminlte-alert theme="success" title="Berhasil" class="mt-3">
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

@endsection
