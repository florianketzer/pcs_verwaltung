@extends('layouts.app')

@section('content')
    
<div class="container">

    <h2 class="underline">Kunde anlegen</h2>

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div>{{$error}}</div>
        @endforeach
    @endif

    {{-- <form id="kunde-anlegen-form" action="{{route('save_customer')}}" method="POST"> --}}
        @if($customer->id)
            <form action="{{ route('customers.update',$customer->id) }}" method="POST">
                @csrf
                {{ method_field('PATCH') }}
        @else
            <form action="{{ route('customers.store') }}" method="POST" id="kunde-anlegen-form">
                @csrf
                {{ method_field('POST') }}
        @endif
        <input type="hidden" id="user-id" name="user-id" value="{{$customer->id}}"/>
        <div class="row">
            <div class="col col-xs-4">
                <div class="form-group">
                    <input type="text" id="kundennummer" name="kundennummer" class="form-control" placeholder="Kundennummer"
                        data-bv-notempty="true"
                        data-bv-notempty-message="Bitte eine Kundennummer angeben"
                        value="{{old('kundennummer', $customer->customer_id)}}"
                    />
                </div>
            </div>
            <div class="col col-xs-4">
                <div id="vertrag-liste">
<?php
if(isset($vertrag)) {
    $vDatum = DateTime::createFromFormat('Y-m-d H:i:s', $vertrag['modified']);
?>
        <span class="document" data-document-id="<?php echo $vertrag['id']; ?>"><?php echo $vertrag['name']; ?> <small><em>[<?php echo $vDatum->format('d.m.Y'); ?>]</em></small> <button type="button" class="btn btn-danger btn-xs" onclick="javascript:delDoc(<?php echo $vertrag['id']; ?>);"><i class="glyphicon glyphicon-remove"></i></button></span>
<?php
}
?>
                </div>
            </div>
            {{-- <div class="col col-xs-4">
                <span class="btn btn-info fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>neuen Vertrag hochladen</span>
                    <input id="vertrag-upload" type="file" name="files">
                </span>
            </div> --}}
        </div>
        <div class="row">
            <div class="col col-xs-4">
                <div class="form-group">
                    <input type="text" id="firma" name="firma" class="form-control" placeholder="Firma"
                        data-bv-notempty="true"
                        data-bv-notempty-message="Bitte eine Firma angeben"
                        value="{{$customer->company}}"
                    />
                </div>
                <div class="form-group">
                    <input type="text" id="ansprechpartner" name="ansprechpartner" class="form-control" placeholder="Ansprechpartner"
                        data-bv-notempty="true"
                        data-bv-notempty-message="Bitte einen Ansprechpartner angeben"
                        value="{{$customer->contact}}"
                    />
                </div>
                <div class="form-group">
                    <select id="servicetyp" name="servicetyp" class="form-control">
                        <option value="">Kein Servicetyp</option>
                        @foreach (\App\Models\Servicetype::all() as $servicetype)
                            <option value="{{$servicetype->id}}" {{$servicetype->id == $customer->servicetype_id ? 'selected' : ''}}>{{$servicetype->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col col-xs-4">
                <div class="form-group">
                    <input type="text" id="strasse" name="strasse" class="form-control" placeholder="Strasse/Nr."
                        data-bv-notempty="true"
                        data-bv-notempty-message="Bitte eine Strasse angeben"
                        value="{{$customer->street}}"
                    />
                </div>
                <div class="form-group">
                    <input type="text" id="zusatz" name="zusatz" class="form-control" placeholder="Zusatz" value="{{$customer->addition}}"/>
                </div>
                <div class="row">
                    <div class="form-group col-xs-6">
                        <input type="text" id="plz" name="plz" class="form-control" placeholder="PLZ"
                            data-bv-notempty="true"
                            data-bv-notempty-message="Bitte eine Postleitzahl angeben"
                            value="{{$customer->postcode}}"
                        />
                    </div>
                    <div class="form-group col-xs-6">
                        <input type="text" id="ort" name="ort" class="form-control" placeholder="Ort"
                            data-bv-notempty="true"
                            data-bv-notempty-message="Bitte einen Ort angeben"
                            value="{{$customer->city}}"
                        />
                    </div>
                </div>
            </div>
            <div class="col col-xs-4">
                <div class="form-group">
                    <input type="text" id="telefon" name="telefon" class="form-control" placeholder="Telefon"
                        data-bv-notempty="true"
                        data-bv-notempty-message="Bitte eine Telefonnummer angeben"
                        value="{{$customer->telephone}}"
                    />
                </div>
                <div class="form-group">
                    <input type="text" id="mobil" name="mobil" class="form-control" placeholder="Mobil" value="{{$customer->mobile}}"/>
                </div>
                <div class="form-group">
                    <input type="text" id="email" name="email" class="form-control" required placeholder="E-Mail"
                        data-bv-notempty="true"
                        data-bv-notempty-message="Bitte eine E-Mail angeben"
                        data-bv-emailaddress="true"
                        data-bv-emailaddress-message="Bitte eine gültige E-Mail angeben"
                        value="{{old('email', $customer->user->email ?? '')}}"
                    />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col col-xs-4">
                <div class="form-group">
                    <textarea id="kommentar" name="kommentar" class="form-control" placeholder="Interner Kommentar (zum Kunden)">{{$customer->comment_intern}}</textarea>
                </div>
            </div>
            <!--div class="col col-xs-4">
                <div id="lieferschein-liste">
<?php
if(isset($lieferscheine)) {
    foreach($lieferscheine as $l) {
        $lDatum = DateTime::createFromFormat('Y-m-d H:i:s', $l['modified']);
?>
        <span class="document" data-document-id="<?php echo $l['id']; ?>"><?php echo $l['name']; ?> <small><em>[<?php echo $lDatum->format('d.m.Y'); ?>]</em></small> <button type="button" class="btn btn-danger btn-xs" onclick="javascript:delDoc(<?php echo $l['id']; ?>);"><i class="glyphicon glyphicon-remove"></i></button></span>
<?php
    }
}
?>
                </div>
            </div>
            <div class="col col-xs-4">
                <span class="btn btn-info fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>neue Auftragsbestätigung hochladen</span>
                    <input id="lieferschein-upload" type="file" name="files" multiple>
                </span>
            </div-->
        </div>

        <h3>Serviceverträge</h3>

        <div class="row">
            <div class="col">
                <div style="display: flex; flex-wrap: wrap;">
                    @foreach ($servicecontracts->where('can_expire', false) as $servicecontract)
                        <div style="padding: 10px;">
                            <input type="checkbox" name="servicecontracts[{{$servicecontract->id}}][sc]" id="sc{{$servicecontract->id}}" value="{{$servicecontract->id}}" {{$customer->servicecontracts->contains($servicecontract->id) ? 'checked' : ''}}> <label for="sc{{$servicecontract->id}}">{{$servicecontract->name}}</label><br>
                        </div>
                    @endforeach

                </div>
                <div style="display: flex; flex-wrap: wrap;">

                    @foreach ($servicecontracts->where('can_expire', true) as $servicecontract)
                        <div style="padding: 10px;">
                            <input type="checkbox" name="servicecontracts[{{$servicecontract->id}}][sc]" id="sc{{$servicecontract->id}}" value="{{$servicecontract->id}}" {{$customer->servicecontracts->contains($servicecontract->id) ? 'checked' : ''}}> <label for="sc{{$servicecontract->id}}">{{$servicecontract->name}}</label><br>
                            <input type="date" name="servicecontracts[{{$servicecontract->id}}][expire_at]" for="sc{{$servicecontract->id}}" value="{{$customer->servicecontracts->where('id', $servicecontract->id)->first() ? \Carbon\Carbon::parse($customer->servicecontracts->where('id', $servicecontract->id)->first()->pivot->expire_at)->format('Y-m-d') : ''}}">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <br>
        <br>
        <br>


        <div class="row">
            <div class="col col-xs-4">
                <div class="row">
                    <div class="form-group col-xs-6">
                        <a href="{{route('home')}}" class="btn btn-default">Abbrechen</a>
                    </div>
                    <div class="form-group col-xs-6">
                        <button type="submit" id="speichern" class="btn btn-success" name="save" value="1">Speichern</button>
                    </div>
                </div>
            </div>
            <div class="col col-xs-4">
                <div class="form-group">
                    <button type="submit" id="speichern-und-arbeitsbericht" class="btn btn-success" name="save_new" value="1">Speichern und <strong>Arbeitsbericht</strong> anlegen</button>
                </div>
            </div>
        </div>
    </form>
    
    <div id="message-box"></div>
</div>


@endsection