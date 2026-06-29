@extends('layouts.guest')

@section('content')
    <div class="container">

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="form-group">
                <label for="email" value="">E-Mail</label>
                <input id="email" class="form-control" type="email" name="email" value="{{old('email', $request->email)}}" required autofocus autocomplete="username" />
            </div>

            <div class="form-group">
                <label for="password">Passwort</label>
                <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="form-group">
                <label for="password_confirmation" value="" >Passwort wiederholen</label>
                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <button class="btn btn-primary">
                    {{ __('Reset Password') }}
                </button>
            </div>
        </form>
    </div>
@endsection
{{-- 
<x-guest-layout>
    <authentication-card>
        <x-slot name="logo">
            <authentication-card-logo />
        </x-slot>

        <validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="block">
                <label for="email" value="{{ __('Email') }}" />
                <input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <label for="password" value="{{ __('Password') }}" />
                <input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <button>
                    {{ __('Reset Password') }}
                </button>
            </div>
        </form>
    </authentication-card>
</x-guest-layout> --}}
