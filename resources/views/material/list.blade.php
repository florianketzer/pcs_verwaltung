@extends('layouts.app')

@section('content')
    
<div class="container">
    <form method="post" action="{{ route('materials.store') }}">
        @csrf
        <h2 class="underline">Materialliste</h2>
        <div class="row">
            <div class="col col-xs-12">
                <button type="button" class="btn btn-info" onclick="javascript:addMaterialLine();"><i class="glyphicon glyphicon-plus"></i></button>
                Neue Zeile 
            </div>
        </div>
        <br/>
        <div id="material-liste">

            @foreach($materials as $material)

                <div class="row material-line">
                    <div class="col col-xs-11">
                        <input type="text" name="material[]" class="form-control" placeholder="Materialbezeichnung" value="{{$material->name}}"/>
                    </div>
                    <div class="col col-xs-1">
                        <button type="button" class="btn btn-danger form-control" onclick="javascript:removeMaterialLine(this);"><i class="glyphicon glyphicon-remove"></i></button>
                    </div>
                </div>

            @endforeach
        </div>
        
        <br/>

        <div class="row">
            <div class="col col-xs-12">
                <a href="/" class="btn btn-default">Abbrechen</a>
                <button type="submit" class="btn btn-success">Speichern</button>
            </div>
        </div>
    </form>
    
    <div id="message-box"></div>
</div>

@endsection