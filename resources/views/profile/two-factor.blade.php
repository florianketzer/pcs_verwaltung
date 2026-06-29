@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Zwei-Faktor-Authentifizierung</h2>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            @if(empty(auth()->user()->two_factor_secret))
                <!-- 2FA nicht aktiviert -->
                <h4>2FA aktivieren</h4>
                <p>Klicken Sie auf "Aktivieren" um die Zwei-Faktor-Authentifizierung zu aktivieren.</p>
                
                <form method="POST" action="{{ route('two-factor.enable') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">2FA aktivieren</button>
                </form>
            @else
                <!-- 2FA aktiviert -->
                <h4>2FA ist aktiviert</h4>
                
                <!-- QR-Code anzeigen -->
                <div class="text-center mb-4">
                    <h5>QR-Code für Authenticator-App:</h5>
                    {!! auth()->user()->twoFactorQrCodeSvg() !!}
                    <p class="text-muted">Scannen Sie diesen Code mit Google Authenticator oder einer anderen Authenticator-App</p>
                </div>

                <!-- Recovery Codes -->
                @if(session('showingRecoveryCodes'))
                    <div class="mb-4">
                        <h5>Wiederherstellungscodes:</h5>
                        <div class="bg-light p-3">
                            @foreach(json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
                                <code>{{ $code }}</code><br>
                            @endforeach
                        </div>
                        <p class="text-muted">Speichern Sie diese Codes sicher!</p>
                    </div>
                @endif

                <!-- Buttons -->
                <div class="d-flex gap-2">
                    @if(!session('showingRecoveryCodes'))
                        <a href="{{ route('two-factor.recovery-codes') }}" class="btn btn-info">Recovery Codes anzeigen</a>
                    @endif

                    <form method="POST" action="{{ route('two-factor.recovery-codes') }}">
                        @csrf
                        <button type="submit" class="btn btn-warning">Recovery Codes neu generieren</button>
                    </form>

                    <form method="POST" action="{{ route('two-factor.disable') }}" onsubmit="return confirm('2FA wirklich deaktivieren?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">2FA deaktivieren</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
