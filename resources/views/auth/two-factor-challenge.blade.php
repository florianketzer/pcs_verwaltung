@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Zwei-Faktor-Authentifizierung</h3>
                </div>
                <div class="card-body">
                    <p>Bitte geben Sie den Authentifizierungscode aus Ihrer Authenticator-App ein.</p>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                {{ $error }}
                            @endforeach
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('two-factor.login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="code" class="form-label">Code</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                   id="code" name="code" required autofocus 
                                   maxlength="6" pattern="[0-9]{6}" 
                                   placeholder="123456"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Bestätigen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
