@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
@endsection

@extends('adminlte::page')

@section('title', 'Edit Decoration')

@section('content')
    <div class="content-header">
        <h1>{{ $user->name }} Log Activity</h1>
    </div>

    <x-adminlte-card>
        <?php $index = 0; ?>
        <x-adminlte-datatable id="userTable" :heads="['No.', 'Changer', 'Subject', 'Event', 'Description', 'Date']" striped hoverable bordered with-buttons>
            @foreach ($logs as $l)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $l->causer_type }}</td>
                    <td>{{ $l->subject_type }}</td>
                    <td>{{ $l->event }}</td>

                    {{-- @if ($l->event == 'updated' && $l->subject_type == 'App\Models\Order')
                    <td>{{$l['properties']['old']['total_price']}} -> {{$l['properties']['attributes']['total_price']}}</td>
                    @else
                    <td>{{ $l->properties }}</td>
                    @endif --}}
                    <td>{{$l->description}}</td>
                    <td>{{ $l->created_at }}</td>
                </tr>
                <?php $index++; ?>
            @endforeach
        </x-adminlte-datatable>
    </x-adminlte-card>
@endsection
