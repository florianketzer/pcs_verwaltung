@extends('layouts.app')

@section('content')
    
<div class="container">

    <br><br>
    <a href="{{route('servicecontracts.create')}}" class="btn btn-primary">Neu</a>
    <br><br>

    
    <table class="table table-hover">

        <thead>
            <tr>
                <th>Name</th>
                <th>Hersteller</th>
                <th></th>
            </tr>
        </thead>

        @foreach($servicecontracts as $servicecontract)
            <tr>
                <td>{{$servicecontract->name}}</td>
                <td>{{$servicecontract->producer_name}}</td>
                <td>
                    <a href="{{route('servicecontracts.edit', $servicecontract->id)}}">Bearbeiten</a>
                </td>
            </tr>
        @endforeach
    
    
    </table>

</div>

@endsection