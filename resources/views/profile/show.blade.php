@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
        Profil
    </h2>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="card">
            <div class="card-header">
                <h3>Profil-Einstellungen</h3>
            </div>
            <div class="card-body">
                <p>Verwalten Sie Ihre Kontoeinstellungen und Sicherheitspräferenzen.</p>
                
                <div class="mt-4">
                    <a href="{{ route('two-factor.index') }}" class="btn btn-primary">
                        Zwei-Faktor-Authentifizierung verwalten
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
