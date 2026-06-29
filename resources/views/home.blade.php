@extends('layouts.app')

@section('content')
    
<div class="container">
    <h2 class="underline">Suche</h2>
    <form action="{{route('search')}}" id="suche-form" method="POST">

        @csrf

        <div class="form-group">
            <input type="text" id="suche-firma" name="suche-firma" class="form-control" placeholder="Firma"/>
        </div>
        <div class="form-group">
            <input type="text" id="suche-ansprechpartner" name="suche-ansprechpartner" class="form-control" placeholder="Ansprechpartner"/>
        </div>
        <div class="form-group">
            <input type="text" id="suche-kundennummer" name="suche-kundennummer" class="form-control" placeholder="Kundennummer"/>
        </div>
        <div class="form-group">
            <input type="hidden" value="0" name="archiv">
            <input type="checkbox" value="1" name="archiv" id="archiv"> <label for="archiv">Archiv</label>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-default btn-">Suchen</button>
        </div>
    </form>
    
    @if($result)
        @if($customers->count()>0)
            <div id="ergebnis">
                <h2 class="underline">Suchergebnisse</h2>

                <table class="suchergebnis table">
                    <tr>
                        <th>
                            Firma
                        </th>
                        <th>
                            Ansprechpartner
                        </th>
                        <th>
                            Arbeitsberichte
                        </th>
                        <th>
                            Bearbeiter
                        </th>
                        <th>
                            Letzter Stand
                        </th>
                    </tr>
                    @foreach ($customers as $customer)
                        {{-- <tr>
                            <td colspan="5">
                                {{$customer}}<br><br>
                                {{$customer->workreports}}<br><br>
                            </td>
                        </tr> --}}
                        <tr class="kunde">
                            <td><a href="{{route('customer', $customer->id)}}">{{$customer->company ?? 'Kein Firmenname'}}</a></td>
                            <td>{{$customer->contact}}</td>
                            <td>{{$customer->workreports->count()}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><a href="{{route('arbeitsbericht', $customer->id)}}" class="btn btn-info btn-sm"><i class="glyphicon glyphicon-plus"></i> neuen Arbeitsbericht hinzufügen</a></td>
                            <td></td>
                            <td>
                                @foreach($customer->workreports->sortByDesc('updated_at') as $workreport)
                                    @if($workreport->work_finished)
                                        <a href="{{route('arbeitsbericht', [$customer->id, $workreport->id])}}" style="white-space: nowrap;"># {{$workreport->number}}</a><br/>
                                    @else
                                        <a href="{{route('arbeitsbericht', [$customer->id, $workreport->id])}}" style="white-space: nowrap;"># [Arbeit nicht beendet]</a><br/>
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                @foreach($customer->workreports->sortByDesc('updated_at') as $workreport)
                                    <span style="white-space: nowrap;">{{$workreport->editor->customer ? $workreport->editor->customer->contact : ""}}</span><br/>
                                @endforeach
                            </td>
                            <td>
                                @foreach($customer->workreports->sortByDesc('updated_at') as $workreport)
                                    <span style="white-space: nowrap;">{{$workreport->updated_at->format('d.m.Y H:i')}}</span><br>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @else
            <p>
                Kein Suchergebnis. Wollen Sie den Kunden neu anlegen?<br/>
                Nach der Neukundenanlage können Sie einen Auftrag hinzufügen...
            </p>
            <a class="btn btn-info" href="{{route('customer')}}"><i class="glyphicon glyphicon-plus"></i> neuen Kunden anlegen</a>
        @endif
    @endif
</div>


@endsection