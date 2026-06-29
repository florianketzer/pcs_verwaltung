@extends('layouts.app')

@section('content')
    <div class="container">
        <h3 class="underline"><i class="glyphicon glyphicon-file"></i> Arbeitsbericht</h3>
        <div class="row">
            <div class="col col-xs-6">
                <input type="text" class="form-control" id="arbeitsbericht-nr" name="arbeitsbericht-nr" placeholder="Auftrag/Arbeitsbericht-Nr." value="{{$workreport->number}}" readonly/>
            </div>
        </div>
        <br/>
{{-- <?php
if($vertrag) {
?>
        <div class="row">
            <div class="col col-xs-12">
                <button type="button" onclick="javascript:toggleVertrag();" class="btn btn-default">
                    <i class="glyphicon glyphicon-play-circle"></i>
                    Leasing-Vertrag/Übernahmevereinbarung anzeigen/ausblenden
                </button>
                <a href="<?php echo DOC_PATH . $vertrag['name']; ?>" class="btn btn-default" target="_blank">
                    <i class="glyphicon glyphicon-book"></i>
                    PDF anzeigen
                </a>
            </div>
        </div>
        <br/>
<?php
}
?> --}}
        <div class="row">
            <div class="col col-xs-12">
                @foreach($workreport->documents->where('type', 'lieferschein') as $document)
                    {{-- <button type="button" onclick="javascript:toggleLieferschein('{{$document->id}}');" class="btn btn-default">
                        <i class="glyphicon glyphicon-play-circle"></i>
                        Auftragsbestätigung anzeigen/ausblenden
                    </button> --}}
                    <?php
                        // $path = $_SERVER['DOCUMENT_ROOT']."/upload/".$lieferschein['name'];
                        // echo "<br>".$path."<br>";
                        // $path = preg_replace("/\s/", "\%20", $_SERVER['DOCUMENT_ROOT']."/upload/".$lieferschein['name']);
                        // echo $path."<br>";
                        // $path = $_SERVER['DOCUMENT_ROOT']."/upload/hall o.txt";
                        // if( file_exists($path)) {
                        //     echo "JA";
                        // } else {
                        //     echo "NEIN";
                        // }
                    ?>
                    <a href="{{asset('storage/upload/'.$document->name)}}" class="btn btn-default" target="_blank">
                        <i class="glyphicon glyphicon-book"></i>
                        PDF anzeigen
                    </a>
                    <a href="javascript:deleteLieferschein('{{$document->id}}')" class="btn btn-danger">
                        <i class="glyphicon glyphicon-trash"></i>
                        Löschen
                    </a>
                    <br/><br/>
                @endforeach
                
				<span class="btn btn-info fileinput-button">
					<i class="glyphicon glyphicon-plus"></i>
					<span>neue Auftragsbestätigung hochladen</span>
                    <form method="post" action="{{route('upload')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="uid" value="{{$workreport->user_id}}"/>
                        <input type="hidden" name="aid" value="{{$workreport->id}}"/>
                        <input type="hidden" name="type" value="lieferschein"/>
					    <input id="lieferschein-upload_" type="file" name="files" onchange="this.form.submit()">
                    </form>
				</span>
            </div>
        </div>
        <br/>
        
        <div class="row">
            <div class="col col-xs-12">
{{-- <?php
if($zusatzdokument) {
?>
                <a href="<?php echo DOC_PATH . $zusatzdokument['name']; ?>" class="btn btn-default" target="_blank">
                    <i class="glyphicon glyphicon-book"></i>
                    Service-Email anzeigen
                </a>
<?php
}
?> --}}
                @foreach($workreport->documents->where('type', 'zusatzdokument') as $document)
                    <a href="{{asset('storage/upload/'.$document->name)}}" class="btn btn-default" target="_blank">
                        <i class="glyphicon glyphicon-book"></i>
                        Service-Email anzeigen
                    </a>
                    <a href="javascript:deleteLieferschein('{{$document->id}}')" class="btn btn-danger">
                        <i class="glyphicon glyphicon-trash"></i>
                        Löschen
                    </a>
                    <br><br>
                @endforeach
                <span class="btn btn-info fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>neue Service-Email hochladen</span>
                    <form method="post" action="{{route('upload')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="uid" value="{{$workreport->user_id}}"/>
                        <input type="hidden" name="aid" value="{{$workreport->id}}"/>
                        <input type="hidden" name="type" value="zusatzdokument"/>
                        <input id="zusatzdokument-upload_" type="file" name="files" onchange="this.form.submit()">
                    </form>
                </span>
            </div>
        </div>
        <br/>

    <form id="arbeitsbericht-form" method="POST" action="{{ route('workreports.store') }}">
        @csrf
        <input type="hidden" id="uid" name="uid" value="{{$user->id}}"/>
        <input type="hidden" id="aid" name="aid" value="{{$workreport->id}}"/>
        <input type="hidden" id="" name="userid" value="{{$customer->user_id}}">
        <input type="hidden" name="cid" value="{{$workreport->user_id}}"/>
        <section id="area-kundeninfo">
            <h4 class="underline">Kundeninformationen</h4>
            <table class="table customerdata">
                <tr>
                    <td class="tag">Kundennummer</td>
                    <td>{{$customer->customer_id ?? ''}}</td>
                    <td class="tag">Strasse/Nr.</td>
                    <td>{{$customer->street ?? ''}}</td>
                    <td class="tag">Telefon</td>
                    <td>{{$customer->telephone ?? ''}}</td>
                </tr>
                <tr>
                    <td class="tag">Firma</td>
                    <td>{{$customer->company ?? ''}}</td>
                    <td class="tag">Zusatz</td>
                    <td>{{$customer->addition ?? ''}}</td>
                    <td class="tag">Mobil</td>
                    <td>{{$customer->mobile ?? ''}}</td>
                </tr>
                <tr>
                    <td class="tag">Ansprechpartner</td>
                    <td>{{$customer->contact ?? ''}}</td>
                    <td class="tag">PLZ/Ort</td>
                    <td>{{$customer->postcode ?? ''}} {{$customer->city ?? ''}}</td>
                    <td class="tag">E-Mail</td>
                    <td>{{$customer->user->email ?? ''}}</td>
                </tr>
            </table>
        </section>
{{-- <?php
if($vertrag) {
?>
        <section id="area-vertrag" style="display:none;">
            <img src="<?php echo DOC_PATH . $vertrag['name'] . '.jpg'; ?>" class="img-responsive"/>
        </section>
<?php
}
?> --}}
{{-- <?php
if($lieferscheine) {
    foreach($lieferscheine as $lieferschein) {
    ?>
            <section id="area-lieferschein-<?php echo $lieferschein['id']; ?>" class="area-lieferschein" style="display:none;">
                <img src="<?php echo DOC_PATH . $lieferschein['name'] . '.jpg'; ?>" class="img-responsive"/>
            </section>
    <?php
    }
}
?> --}}
        {{-- <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-info">
                    <div class="panel-heading">Legende</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-6 col-md-4">
                                <i class="glyphicon glyphicon-plus"></i> Datensatz hinzufügen
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <i class="glyphicon glyphicon-minus"></i> Datensatz entfernen
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Beginn nicht benötigtes Material (Korrektur zum Auftrag) -->
        
        <h4 class="underline">- Korrektur zum Auftrag</h4>
        <div class="row">
            <div class="col col-xs-4">
                <table class="input">
                    <tr>
                        <td>
                            <label>Pos.Nr.</label>
                            <input type="text" class="form-control" id="unused-posnr-prepare"/>
                        </td>
                        <td>
                            <label>Menge</label>
                            <input type="text" class="form-control" id="unused-menge-prepare"/>
                        </td>
                        <td>
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-info form-control" id="add-unused-material" <?php echo $workreport->locked; ?>><i class="glyphicon glyphicon-plus"></i></button>
                        </td>
                    </tr>
                </table>
            </div>
        </div> --}}
        
        <section id="area-kundeninfo">
            <h4 class="underline">Serviceverträge</h4>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Produkt</th>
                        <th>gültig bis</th>
                    </tr>
                </thead>
                @foreach($customer->servicecontracts as $sc)
                    @php
                        $now = \Carbon\Carbon::now()->subDay();
                        $expire = \Carbon\Carbon::parse($sc->pivot->expire_at);

                        $color = "";
                        $badge = "default";

                        // Bedingung 1: Ablaufdatum ist größer als 6 Monate
                        if ($expire->diffInDays($now) >= 180) {
                            $color = 'green';
                            $badge = 'success';
                        }

                        // Bedingung 2: Ablaufdatum ist größer als 3 Monate und kleiner als 6 Monate
                        if ($expire->diffInDays($now) <= 90) {
                            $color = 'yellow';
                            $badge = 'warning';
                        }

                        // Bedingung 3: Ablaufdatum ist abgelaufen
                        if ($expire->isPast()) {
                            $color = 'red';
                            $badge = 'danger';
                        }
                    @endphp

                    <tr>
                        <td>
                            @if($sc->can_expire)
                                {{$sc->producer_name}}<br>
                                <span class="small">{{$sc->name}}</span>
                            @else
                                {{$sc->name}}
                            @endif
                        </td>
                        @if($sc->can_expire)
                            <td style="_background-color: {{$color}}">
                                <span class="label label-{{$badge}}" style="font-size: 14px;">
                                {{$sc->pivot->expire_at ? \Carbon\Carbon::parse($sc->pivot->expire_at)->format('d.m.Y') : ''}}
                                {{-- ({{$expire->diffInDays($now)}} Tage) --}}
                            </span>
                        @else
                            <td>
                                &infin;
                        @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        </section>

        <hr class="dashed"/>
        
        <div id="unused-material-liste" class="row">

            @foreach($workreport->notusedmaterials as $notusedmaterial)
                <div class="col col-xs-4 unused-material-line">
                    <table class="input">
                        <tr>
                            <td>
                                <label>Pos.Nr.</label>
                                <input type="text" class="form-control" name="unused-posnr[]" value="{{$notusedmaterial->posnr}}"/>
                            </td>
                            <td>
                                <label>Menge</label>
                                <input type="text" class="form-control" name="unused-menge[]" value="{{$notusedmaterial->quantity}}"/>
                            </td>
                            <td>
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-danger form-control" onclick="javascript:removeUnusedMaterial(this);" {{$workreport->locked ? 'disabled' : ''}}><i class="glyphicon glyphicon-remove"></i></button>
                            </td>
                        </tr>
                    </table>
                </div>
            @endforeach

        </div>
        
        <!-- Ende nicht benötigtes Material (Korrektur zum Auftrag) -->
        
        <!-- Beginn benötigtes Material (Korrektur zum Auftrag) -->
        
        <h4 class="underline">+ Benötigtes Montagematerial</h4>
        <div class="row">
            <div class="col col-xs-8">
                <label for="zusatz-material">Materialauswahl</label>
                <select id="zusatz-material-select" name="zusatz-material-select" class="form-control">
                    <option value="">Bitte auswählen</option>
                        @foreach($materials as $material) {
                            <option value="{{$material->name}}">{{$material->name}}</option>
                        @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col col-xs-8">
                <label>Materialbezeichnung</label>
                <input id="zusatz-material-prepare" name="zusatz-material-prepare" class="form-control"/>
            </div>
            <div class="col col-xs-2">
                <label>Menge</label>
                <input id="zusatz-material-menge-prepare" name="zusatz-material-menge-prepare" type="text" class="form-control" />
            </div>
            <div class="col col-xs-2">
                <label>Aktion</label>
                <button class="btn btn-info form-control" type="button" id="add-additional-material" {{$workreport->locked ? 'disabled' : ''}}><i class="glyphicon glyphicon-plus"></i></button>
            </div>
        </div>
        
        <hr class="dashed"/>
        
        <div id="zusatzmaterial-liste">
            @foreach($workreport->additionalmaterials as $additionalmaterial)
                <div class="row zusatzmaterial-line">
                    <div class="col col-xs-8">
                        <label for="zusatz-material">Materialbezeichnung</label>
                        <input type="text" name="zusatz-material[]" class="form-control" value="{{$additionalmaterial->designation}}"/>
                    </div>
                    <div class="col col-xs-2">
                        <label>Menge</label>
                        <input name="zusatz-material-menge[]" type="text" class="form-control" value="{{$additionalmaterial->quantity}}"/>
                    </div>
                    <div class="col col-xs-2">
                        <label>&nbsp;</label>
                        <button class="btn btn-danger form-control" type="button" onclick="javascript:removeZusatzMaterial(this);" {{$workreport->locked ? 'disabled' : ''}}><i class="glyphicon glyphicon-remove"></i></button>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Ende benötigtes Material (Korrektur zum Auftrag) -->
        
        <h4 class="underline">+ Sonstige Informationen</h4>
        <div class="row">
            <div class="col col-xs-12">
                <textarea class="form-control" id="sonstiges" name="sonstiges">{{$workreport->comment}}</textarea>
            </div>
        </div>
        
        <!-- Beginn Arbeitszeiten -->
        
        <h4 class="underline">Arbeitszeiten</h4>
        <div class="row">
            <div class="col col-xs-12">
                <table class="input">
                    <tr class="arbeitszeit-line prepare">
                        <td>
                            <label>Arbeitsdatum</label>
                            <div class="inner-addon left-addon">
                                {{-- <i class="glyphicon glyphicon-calendar"></i> --}}
                                <input type="date" class="form-control" id="arbeitszeit-datum-prepare"/>
                            </div>
                        </td>
                        <td>
                            <label>Reise von</label>
                            <input type="text" class="form-control zeit zeit-reise-von" data-field="time" id="arbeitszeit-reise-von-prepare"/>
                        </td>
                        <td>
                            <label>Arbeit von</label>
                            <input type="text" class="form-control zeit zeit-arbeit-von" id="arbeitszeit-arbeit-von-prepare"/>
                        </td>
                        <td>
                            <label>Arbeit bis</label>
                            <input type="text" class="form-control zeit zeit-arbeit-bis" id="arbeitszeit-arbeit-bis-prepare"/>
                        </td>
                        <td>
                            <label>Reise bis</label>
                            <input type="text" class="form-control zeit zeit-reise-bis" id="arbeitszeit-reise-bis-prepare"/>
                        </td>
                        <td>
                            <label>Reisestunden</label>
                            <input type="text" class="form-control zeit-reisestunden" id="arbeitszeit-reisestunden-prepare" readonly/>
                        </td>
                        <td>
                            <label>Arbeitsstunden</label>
                            <input type="text" class="form-control zeit-arbeitsstunden" id="arbeitszeit-arbeitsstunden-prepare" readonly/>
                        </td>
                        {{-- <td>
                            <label>Überstunden</label>
                            <input type="text" class="form-control zeit zeit-ueberstunden zeit-arbeitsstunden" id="arbeitszeit-ueberstunden-prepare"/>
                        </td> --}}
                        <td>
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-info form-control" onclick="javascript:addArbeitszeiten();" {{$workreport->locked ? 'disabled' : ''}}><i class="glyphicon glyphicon-plus"></i></button>
                        </td>
                    </tr>
					<tr>
						<td/>
						<td colspan="4">
							<div class="no-travel" onclick="javascript:noTravel(this);">Keine Reisezeit</div>
						</td>
						<td colspan="3"/>
					</tr>
                    <tr>
                        <td colspan="8">
                            <textarea class="form-control" placeholder="Durchgeführte Arbeiten" id="durchgefuehrte-arbeiten-prepare"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8">
                            <hr class="dashed"/>
                        </td>
                    </tr>

                    @foreach($workreport->workingtimes->sortBy('date') as $workingtime)
                        <tr class="arbeitszeit-line">
                            <td>
                                <label>Arbeitsdatum</label>
                                <input type="text" class="form-control" name="arbeitszeit-datum[]" value="{{$workingtime->date->format('d.m.Y')}}"/>
                            </td>
                            <td>
                                <label>Reise von</label>
                                <input type="text" class="form-control zeit zeit-reise-von" name="arbeitszeit-reise-von[]" value="{{$workingtime->travel_time_from}}"/>
                            </td>
                            <td>
                                <label>Arbeit von</label>
                                <input type="text" class="form-control zeit zeit-arbeit-von" name="arbeitszeit-arbeit-von[]" value="{{$workingtime->work_from}}"/>
                            </td>
                            <td>
                                <label>Arbeit bis</label>
                                <input type="text" class="form-control zeit zeit-arbeit-bis" name="arbeitszeit-arbeit-bis[]" value="{{$workingtime->work_to}}"/>
                            </td>
                            <td>
                                <label>Reise bis</label>
                                <input type="text" class="form-control zeit zeit-reise-bis" name="arbeitszeit-reise-bis[]" value="{{$workingtime->travel_time_to}}"/>
                            </td>
                            <td>
                                <label>Reisestunden</label>
                                <input type="text" class="form-control zeit-reisestunden" readonly/>
                            </td>
                            <td>
                                <label>Arbeitsstunden</label>
                                <input type="text" class="form-control zeit-arbeitsstunden" readonly/>
                            </td>
                            {{-- <td>
                                <label>Überstunden</label>
                                <input type="text" class="form-control zeit zeit-ueberstunden" name="arbeitszeit-ueberstunden[]" value="{{$workingtime->overtime}}"/>
                            </td> --}}
                            <td>
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-danger form-control" onclick="javascript:removeArbeitszeit(this);" {{$workreport->locked ? 'disabled' : ''}}><i class="glyphicon glyphicon-remove"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td/>
                            <td colspan="4">
                                <div class="no-travel" onclick="javascript:noTravel(this);">Keine Reisezeit</div>
                            </td>
                            <td colspan="3"/>
                        </tr>
                        <tr>
                            <td colspan="8">
                                <textarea class="form-control" placeholder="Durchgeführte Arbeiten" name="durchgefuehrte-arbeiten[]">{{$workingtime->text}}</textarea>
                            </td>
                        </tr>
                    @endforeach

                    <tr id="arbeitszeiten-insert-marker" class="summary-header">
                        <td colspan="5"/>
                        <td>
                            Reisestunden
                        </td>
                        <td>
                            Arbeitsstunden
                        </td>
                        {{-- <td>
                            Überstunden
                        </td> --}}
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" class="form-control" id="arbeit-beendet" name="arbeit-beendet" {{$workreport->work_finished ? 'checked' : ''}} {{$workreport->locked ? 'disabled' : ''}}/>
                        </td>
                        <td colspan="2">
                            Arbeit beendet
                        </td>
                        <td/>
                        <td>
                            Summen
                        </td>
                        <td>
                            <input id="summe-reisestunden" type="text" class="form-control" readonly/>
                        </td>
                        <td>
                            <input id="summe-arbeitsstunden" type="text" class="form-control" readonly/>
                        </td>
                        {{-- <td>
                            <input id="summe-ueberstunden" type="text" class="form-control" readonly/>
                        </td> --}}
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Ende Arbeitszeiten -->
        
        <hr/>
        
        <div id="section-unterschriften">
            <div class="row">
                <div class="col col-xs-6">
                    <label>Unterschrift PCS Kundendienst</label>
                    <button type="button" class="btn btn-default form-control" id="add-unterschrift-kundendienst" {{$workreport->locked ? 'disabled' : ''}}><i class="glyphicon glyphicon-pencil"></i></button>

                    @if(is_file(storage_path('app/public/upload/').'/unterschrift_kundendienst_' . $workreport->id . '.png'))
                        <img src="{{asset('storage/upload'.'/unterschrift_kundendienst_' . $workreport->id . '.png')}}" id="img-unterschrift-kundendienst" class="img-responsive"/>
                    @else
                        <img id="img-unterschrift-kundendienst" class="img-responsive"/>
                    @endif
                </div>
                <div class="col col-xs-6">
                    <label>Unterschrift Kunde oder dessen Stellvertreter</label>
                    <button type="button" class="btn btn-default form-control" id="add-unterschrift-kunde" {{$workreport->locked ? 'disabled' : ''}}><i class="glyphicon glyphicon-pencil"></i></button>

                    @if(is_file(storage_path('app/public/upload/').'/unterschrift_kunde_' . $workreport->id . '.png'))
                        <img src="{{asset('storage/upload'.'/unterschrift_kunde_' . $workreport->id . '.png')}}" id="img-unterschrift-kunde" class="img-responsive"/>
                    @else
                        <img id="img-unterschrift-kunde" class="img-responsive"/>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col col-xs-2 col-xs-offset-4">
                    <label>Datum</label>
                    <div class="inner-addon left-addon">
                        {{-- <i class="glyphicon glyphicon-calendar"></i> --}}
                        <input type="date" class="form-control" id="arbeitsbericht-datum" name="arbeitsbericht-datum"
                        value="{{$workreport->work_finished ? $workreport->date->format('Y-m-d') : \Carbon\Carbon::now()->format('Y-m-d')}}"
                        />
                    </div>
                </div>
                <div class="col col-xs-6">
                    <p class="fineprint">
                        Die ordnungsgemäße Ausführung der Arbeiten bestätigt.<br/>
                        Dieses Dokument ist die Grundlage für die Rechnungslegung.
                    </p>
                </div>
            </div>
        </div>
        <div id="section-unterschrift-kundendienst" class="row" style="display:none;">
            <div class="col col-xs-12">
                <h4>Unterschrift Kundendienst</h4>
            </div>
            <div class="col col-xs-11">
                <canvas id="canvas-unterschrift-kundendienst" class="unterschrift" width="650" height="200"></canvas>
                <input id="name-kundendienst-ausgeschrieben" type="text" class="form-control" placeholder="Name ausgeschrieben"/>
            </div>
            <div class="col col-xs-1">
                <button type="button" class="btn btn-info" id="btn-unterschrift-kundendienst-speichern"><i class="glyphicon glyphicon-ok"></i></button>
                <br/><br/>
                <button type="button" class="btn btn-default" id="btn-unterschrift-kundendienst-refresh"><i class="glyphicon glyphicon-refresh"></i></button>
                <br/><br/>
                <button type="button" class="btn btn-danger" id="btn-unterschrift-kundendienst-loeschen"><i class="glyphicon glyphicon-remove"></i></button>
            </div>
        </div>
        <div id="section-unterschrift-kunde" class="row" style="display:none;">
            <div class="col col-xs-12">
                <h4>Unterschrift Kunde oder dessen Stellvertreter</h4>
            </div>
            <div class="col col-xs-11">
                <canvas id="canvas-unterschrift-kunde" class="unterschrift" width="650" height="200"></canvas>
                <input id="name-kunde-ausgeschrieben" type="text" class="form-control" placeholder="Name ausgeschrieben"/>
            </div>
            <div class="col col-xs-1">
                <button type="button" class="btn btn-info" id="btn-unterschrift-kunde-speichern"><i class="glyphicon glyphicon-ok"></i></button>
                <br/><br/>
                <button type="button" class="btn btn-default" id="btn-unterschrift-kunde-refresh"><i class="glyphicon glyphicon-refresh"></i></button>
                <br/><br/>
                <button type="button" class="btn btn-danger" id="btn-unterschrift-kunde-loeschen"><i class="glyphicon glyphicon-remove"></i></button>
            </div>
        </div>
            
        <hr/>
        <div class="row">
            <div class="col col-xs-4 form-inline">
                <a href="/" class="btn btn-default">Abbrechen</a>

                <button type="submit" id="speichern" class="btn btn-success" {{$workreport->locked ? 'disabled' : ''}}>Speichern</button>
            </div>
            <div class="col col-xs-8 form-inline">
                <div class="form-group">
                    <input type="text" class="form-control" id="email" name="email" placeholder="E-Mail" required
                        data-bv-notempty="true"
                        data-bv-notempty-message="Bitte eine E-Mail angeben"
                        data-bv-emailaddress="true"
                        data-bv-emailaddress-message="Bitte eine gültige E-Mail angeben"
                        value="{{$customer->user->email ?? ''}}"
                    />
                </div>

                <button type="submit" id="speichern-und-senden" class="btn btn-success" {{$workreport->locked ? 'disabled' : ''}}>Speichern und <strong>Auftrag</strong> verschicken</button>
                @if(!$workreport->locked)
                    <button type="button" class="btn btn-danger pull-right" id="btn-loeschen">Bericht löschen</button>
                @endif
            </div>
        </div>

        @if($workreport->locked && auth()->user()->usergroups->contains(3))
            <div class="row">
                <div class="col-xs-12">
                    <br/>
                    <button type="button" id="btn-unlock" class="btn btn-warning">Bericht entsperren</button>
                </div>
            </div>
        @endif
        
        <div id="message-box"></div>
    </div>
    </form>


@endsection