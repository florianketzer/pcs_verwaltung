<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Laravel\Fortify\Contracts\RecoveryCodesGeneratedResponse;

class TwoFactorRecoveryCodesController extends Controller
{
    /**
     * Zeige die Wiederherstellungscodes-Seite an.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('profile.two-factor-recovery-codes');
    }

    /**
     * Generiere neue Wiederherstellungscodes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Fortify\Actions\GenerateNewRecoveryCodes  $generate
     * @return \Laravel\Fortify\Contracts\RecoveryCodesGeneratedResponse
     */
    public function store(Request $request, GenerateNewRecoveryCodes $generate)
    {
        $generate($request->user());

        return redirect()->route('two-factor.recovery-codes')->with('status', 'Neue Wiederherstellungscodes wurden erfolgreich generiert!');
    }
}
