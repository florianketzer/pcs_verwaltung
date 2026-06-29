@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Wiederherstellungscodes</h3>
                </div>

                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    @if(auth()->user()->two_factor_secret && auth()->user()->two_factor_recovery_codes)
                        <div class="alert alert-warning">
                            <h5 class="alert-heading">Wichtiger Hinweis!</h5>
                            <p class="mb-0">
                                Speichern Sie diese Wiederherstellungscodes an einem sicheren Ort. 
                                Sie können verwendet werden, um Zugriff auf Ihr Konto wiederherzustellen, 
                                wenn Ihr Zwei-Faktor-Authentifizierungsgerät verloren geht.
                            </p>
                        </div>

                        <div class="bg-light p-4 rounded mb-4">
                            <h5 class="mb-3">Ihre Wiederherstellungscodes:</h5>
                            <div class="row">
                                @foreach(json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
                                    <div class="col-md-6 mb-2">
                                        <code class="d-block p-2 bg-white border rounded">{{ $code }}</code>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <form method="POST" action="{{ route('two-factor.recovery-codes') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning" onclick="return confirm('Sind Sie sicher, dass Sie neue Wiederherstellungscodes generieren möchten? Die alten Codes werden ungültig!')">
                                    <i class="fas fa-sync-alt"></i> Neue Codes generieren
                                </button>
                            </form>

                            <a href="{{ route('two-factor.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Zurück zur 2FA-Verwaltung
                            </a>
                        </div>

                        <div class="mt-4">
                            <small class="text-muted">
                                <strong>Tipp:</strong> Drucken Sie diese Codes aus oder speichern Sie sie in einem sicheren Passwort-Manager.
                            </small>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <h5 class="alert-heading">Keine Wiederherstellungscodes verfügbar</h5>
                            <p class="mb-0">
                                Sie haben noch keine Zwei-Faktor-Authentifizierung aktiviert oder keine Wiederherstellungscodes generiert.
                            </p>
                        </div>

                        <a href="{{ route('two-factor.index') }}" class="btn btn-primary">
                            <i class="fas fa-shield-alt"></i> Zur 2FA-Verwaltung
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
