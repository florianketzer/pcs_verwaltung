<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <link href="/css/main.css" rel="stylesheet">

    <style>
        body {
            font-family: Helvetica, Sans-Serif;
            font-size: 10px;
        }

        .gray {
            color: #AAAAAA;
        }

        .bezeichner {
            color: #AAAAAA;
        }
        table {
            width: 100%
        }
        .img-responsive {
            display: block;
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>

    <img src="{{asset('images/pcs_logo_new.png')}}">

    <h2>Arbeitsbericht # {{$workreport->number}}</h2><br/>

    <h3>Kundendaten</h3>
    <table>
        <tr>
            <th class="col1"></th>
            <th class="col2"></th>
            <th class="col3"></th>
            <th class="col4"></th>
            <th class="col5"></th>
            <th class="col6"></th>
        </tr>
        <tr>
            <td class="bezeichner">Kundennummer</td>
            <td>{{$customer->customer_id}}</td>
            <td class="bezeichner">Strasse/Nr.</td>
            <td>{{$customer->street}}</td>
            <td class="bezeichner">Telefon</td>
            <td>{{$customer->telephone}}</td>
        </tr>
        <tr>
            <td class="bezeichner">Firma</td>
            <td>{{$customer->company}}</td>
            <td class="bezeichner">Zusatz</td>
            <td>{{$customer->addition}}</td>
            <td class="bezeichner">Mobil</td>
            <td>{{$customer->mobile}}</td>
        </tr>
        <tr>
            <td class="bezeichner">Ansprechpartner</td>
            <td>{{$customer->contact}}</td>
            <td class="bezeichner">PLZ/Ort</td>
            <td>{{$customer->zip}} {{$customer->city}}</td>
            <td class="bezeichner">E-Mail</td>
            <td>{{$customer->user->email}}</td>
        </tr>
    </table>
    <br/>
    <br/>

    @if($customer->servicecontracts->count()>0)
        <h3>Serviceverträge</h3>
        <table class="table table-hover">
            <thead>
                <tr>
                    <td><b>Produkt</b></td>
                    <td><b>gültig bis</b></td>
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
                            <span class="label label-{{$badge}}" style="">
                            {{$sc->pivot->expire_at ? \Carbon\Carbon::parse($sc->pivot->expire_at)->format('d.m.Y') : ''}}
                            {{-- ({{$expire->diffInDays($now)}} Tage) --}}
                        </span>
                    @else
                        <td>
                            <img src="{{asset('images/unendlich.png')}}">
                    @endif
                    </td>
                </tr>
            @endforeach
        </table>
        <br>
        <br>
    @endif

    @if($workreport->notusedmaterials->count()>0)
        <h3>Korrektur zum Auftrag</h3>
        <table>
            <colgroup>
                <col>
                <col width="120">
            </colgroup>
            <tr>
                <td><b>Pos.Nr.</b></td>
                <td><b>Menge</b></td>
            </tr>
            @foreach($workreport->notusedmaterials as $notusedmaterial)
                <tr>
                    <td>{{$notusedmaterial->posnr}}</td>
                    <td>{{$notusedmaterial->quantity}}</td>
                </tr>
            @endforeach
        </table>
        <br/>
        <br/>
    @endif

    <h3>Benötigtes Montagematerial</h3>
    <table>
        <colgroup>
            <col>
            <col width="120">
        </colgroup>
        <tr>
            <td><b>Bezeichnung</b></td>
            <td><b>Menge</b></td>
        </tr>
        @foreach($workreport->additionalmaterials as $additionalmaterial)
            <tr>
                <td style="border-bottom: 1px solid #000000;">{{$additionalmaterial->designation}}</td>
                <td style="border-bottom: 1px solid #000000;">{{$additionalmaterial->quantity}}</td>
            </tr>
        @endforeach
    </table>
    <br/>
    <br/>

    <h3>Sonstige Informationen</h3>
    <p>{!!nl2br($workreport->comment)!!}</p>
    <br/>

    @php
        function toReadableTime($theTime) {
            $theTime /= 60;
            $minutes = $theTime % 60;
            $theTime /= 60;
            $hours = $theTime % 24;
            return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
        }
        function aufrunden($zeit, $min) {
            $ROUNDING = $min * 60; // 15 min in ms
            return ceil(($zeit) / $ROUNDING) * $ROUNDING; // auf 15 min aufrunden
        }
        function calculateHours($sumHours) {
            $minutes        = $sumHours / 60 % 60;
            $hours          = ($sumHours / 60) / 60;
            $toString       = sprintf($hours);
            $roundesHours   = rtrim(substr($toString, 0, 2), ".");
            //return 'Stunden' .  $hours . ' / ' . 'Minuten' . $minutes;
            return str_pad($roundesHours<=9?"0".$roundesHours:$roundesHours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
            //return str_pad($minutes, 2, '0', STR_PAD_LEFT);
        }
    @endphp

    <h3>Arbeitszeiten</h3>
    <table border="1" cellspacing="0">
        <tr>
            <td><b>Arbeitsdatum</b></td>
            <td><b>Reise von</b></td>
            <td><b>Arbeit von</b></td>
            <td><b>Arbeit bis</b></td>
            <td><b>Reise bis</b></td>
            <td><b>Reisestunden</b></td>
            <td><b>Arbeitsstunden</b></td>
            {{-- <th class="col8">Überstunden</th> --}}
        </tr>
        @php
            $summeArbeitsstunden = 0;
            $summeReisestunden = 0;
            $summeUeberstunden = 0;

            // Funktion zum Umwandeln von Sekunden in Stunden und Minuten
            function sekundenZuStundenMinuten($sekunden) {
                $stunden = floor($sekunden / 3600);
                $minuten = floor(($sekunden % 3600) / 60);
                return sprintf('%02d:%02d', $stunden, $minuten);
            }
        @endphp
        @foreach($workreport->workingtimes->sortBy('date') as $workingtime)
            @php
                $zeitReiseVon = DateTime::createFromFormat('Y-m-d H:i:s', '1900-00-00 ' . $workingtime->travel_time_from);
                $zeitReiseBis = DateTime::createFromFormat('Y-m-d H:i:s', '1900-00-00 ' . $workingtime->travel_time_to);
                if ($zeitReiseBis->getTimestamp() < $zeitReiseVon->getTimestamp()) {
                    $zeitReiseBis->modify('+1 day');
                }
                $zeitArbeitVon = DateTime::createFromFormat('Y-m-d H:i:s', '1900-00-00 ' . $workingtime->work_from);
                $zeitArbeitBis = DateTime::createFromFormat('Y-m-d H:i:s', '1900-00-00 ' . $workingtime->work_to);
                // $zeitUeberstunden = DateTime::createFromFormat('Y-m-d H:i:s', '1900-00-00 ' . $workingtime->overtime);

                $zeitArbeitsstunden = $zeitArbeitBis->format('U') - $zeitArbeitVon->format('U');
                $zeitReisestunden = $zeitReiseBis->format('U') - $zeitReiseVon->format('U') - $zeitArbeitsstunden;
                // $zeitUeberstundenTimestamp = $zeitUeberstunden->format('U') - DateTime::createFromFormat('Y-m-d H:i:s', '1900-00-00 00:00:00')->format('U');
                
                $zeitArbeitsstunden = aufrunden($zeitArbeitsstunden, 15);
                $zeitReisestunden = aufrunden($zeitReisestunden, 15);

                $summeArbeitsstunden += $zeitArbeitsstunden;
                $summeReisestunden += $zeitReisestunden;
                // $summeUeberstunden += $zeitUeberstundenTimestamp;
            @endphp
            <tr>
                <td>
                    {{$workingtime->date->format('d.m.Y')}}
                </td>
                <td>
                    {{\Carbon\Carbon::parse($workingtime->travel_time_from)->format('H:i')}}
                </td>
                <td>
                    {{\Carbon\Carbon::parse($workingtime->work_from)->format('H:i')}}
                </td>
                <td>
                    {{\Carbon\Carbon::parse($workingtime->work_to)->format('H:i')}}
                </td>
                <td>
                    {{\Carbon\Carbon::parse($workingtime->travel_time_to)->format('H:i')}}
                </td>
                <td>
                    {{-- $zeitArbeitsstunden = $zeitArbeitBis->format('U') - $zeitArbeitVon->format('U');
                    $zeitReisestunden = $zeitReiseBis->format('U') - $zeitReiseVon->format('U') - $zeitArbeitsstunden; --}}
                    {{-- @if($workingtime->work_to != "" && $workingtime->work_to != "00:00:00") --}}
                    {{-- {{toReadableTime(aufrunden(\Carbon\Carbon::parse($workingtime->travel_time_from)->diffInHours(\Carbon\Carbon::parse($workingtime->travel_time_to)) - \Carbon\Carbon::parse($workingtime->work_from)->diffInHours(\Carbon\Carbon::parse($workingtime->work_to)), 15))}} --}}
                    {{-- @endif --}}

                    {{toReadableTime($zeitReisestunden)}}
                </td>
                <td>
                    {{toReadableTime($zeitArbeitsstunden)}}
                </td>
                {{-- <td>
                    {{toReadableTime($summeUeberstunden)}}
                </td> --}}
            </tr>
            @if($workingtime->text != "")
                <tr>
                    <td colspan="7">{!!nl2br($workingtime->text)!!}<br><br></td>
                </tr>
            @endif
        @endforeach
        @php
            // Gesamte Arbeits- und Reisestunden in Stunden und Minuten umwandeln
            $summeArbeitsstundenFormatted = sekundenZuStundenMinuten($summeArbeitsstunden);
            $summeReisestundenFormatted = sekundenZuStundenMinuten($summeReisestunden);
        @endphp
        <tr>
            <td colspan="5">
                <b>Summe:</b>
            </td>
            <td>
                {{-- <b>{{toReadableTime($summeReisestunden)}}</b> --}}
                <b>{{$summeReisestundenFormatted}}</b>
            </td>
            <td>
                {{-- <b>{{calculateHours($summeArbeitsstunden)}}</b> --}}
                <b>{{$summeArbeitsstundenFormatted}}</b>
            </td>
            {{-- <td>
                {{toReadableTime($summeUeberstunden)}}
            </td> --}}
        </tr>
        @if($workreport->work_finished)
            <tr>
                <td>Arbeit beendet</td>
                <td colspan="6">
                    <span style="color:green; font-weight: bold;"><img src="{{asset('images/checkmark.png')}}" style="height: 15px;"></span>
                </td>
            </tr>
        @endif
    </table>
    <br>
    <br>

    <h4>Wichtiger Hinweis:</h4>
    <p>Alle im Rahmen einer kostenpflichtigen Serviceleistung durch Tauschteil ersetzte Teile stehen max. 14 Tage für evtl. Begutachtung von Versicherungen oder Sachverständigen bei uns zur Verfügung.</p>
    <p>Vorgenannte Systeme/Geräte/Dienstleistungen bestellt der Auftraggeber zu den beim Auftragnehmer gültigen "VERTRAGSBEDINGUNGEN" sowie dessen listenmäßigen Preisen. Bei Miete gelten die Bedingungen des bestehenden Mietvertrages. Der Mietpreis errechnet sich aus der Netto-Kaufsumme bezogen auf die Restlaufzeit des bestehenden Mietvertrages, zzgl. Instandhaltungskosten. DER KUNDE BESTÄTIGT, DASS ER DIE VERTRAGSBEDINGUNGEN ERHALTEN HAT.</p>
    <p>Betriebsbereit mit Einweisung ohne Mängel</p>
    <p>Die ordnungsgemäße Ausführung der Arbeiten bestätigt. Dieses Dokument ist die Grundlage für die Rechnungslegung.</p>
    <br/>
    <br/>

    <table>
        <tr>
            <td>
                @if(is_file(storage_path('app/public/upload/').'/unterschrift_kundendienst_' . $workreport->id . '.png'))
                    <img src="{{asset('storage/upload'.'/unterschrift_kundendienst_' . $workreport->id . '.png')}}" id="img-unterschrift-kundendienst" class="img-responsive"/>
                @endif
            </td>
            <td>
                @if(is_file(storage_path('app/public/upload/').'/unterschrift_kunde_' . $workreport->id . '.png'))
                    <img src="{{asset('storage/upload'.'/unterschrift_kunde_' . $workreport->id . '.png')}}" id="img-unterschrift-kunde" class="img-responsive"/>
                @endif
            </td>
        </tr>
        <tr>
            <td>
                {{$workreport->name_customer_service}}
            </td>
            <td>
                {{$workreport->name_customer}}
            </td>
        </tr>
        <tr>
            <td><span class="bezeichner">&lt;Unterschrift PCS Kundendienst&gt;</span></td>
            <td><span class="bezeichner">&lt;Unterschrift Kunde oder dessen Stellvertreter&gt;</span></td>
        </tr>
    </table>
    <br>
    Datum: {{$workreport->date->format('d.m.Y')}}
</body>
</html>

