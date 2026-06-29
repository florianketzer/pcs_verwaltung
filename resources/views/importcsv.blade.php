@extends('layouts.app')

@section('content')
    
<div class="container">

    <h2 class="underline">CSV Serviceverträge importieren</h2>

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div>{{$error}}</div>
        @endforeach
    @endif

    <div class="row">
        <div class="col">
            <form action="{{ route('runimportcsv') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="file" name="csv">


                <div class="form-group">
                    <button type="submit" id="speichern-und-arbeitsbericht" class="btn btn-success">Uploade</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection