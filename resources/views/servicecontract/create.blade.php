@extends('layouts.app')

@section('content')
    <br><br>

<div class="container">

    <form method="post" action="{{ route('servicecontracts.store') }}">
        @csrf
        
        <div class="form-group">
            <label>Name</label>
            <input type="text" class="form-control" name="name">
        </div>

        <div class="form-group">
            <label>Hersteller</label>
            <input type="text" class="form-control" name="producer_name">
        </div>

        <div class="form-group">
            <label>Kann ablaufen</label>
            <input type="hidden" name="can_expire" value="0">
            <input type="checkbox" value="1" name="can_expire">
        </div>
    
        <div class="row">
            <div class="col col-xs-12">
                <a href="{{route('servicecontracts.index')}}" class="btn btn-default">Abbrechen</a>
                <button type="submit" class="btn btn-success">Speichern</button>
            </div>
        </div>
    </form>

</div>

@endsection
