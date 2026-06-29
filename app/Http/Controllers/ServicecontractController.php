<?php

namespace App\Http\Controllers;

use App\Models\Servicecontract;
use Illuminate\Http\Request;

class ServicecontractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $servicecontracts = Servicecontract::all();
        return view('servicecontract.index', compact(['servicecontracts']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('servicecontract.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $servicecontract = new Servicecontract;

        $servicecontract->name = $request->get('name');
        $servicecontract->producer_name = $request->get('producer_name');
        $servicecontract->can_expire = $request->get('can_expire');

        $servicecontract->save();

        return redirect()->route('servicecontracts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Servicecontract  $servicecontract
     * @return \Illuminate\Http\Response
     */
    public function show(Servicecontract $servicecontract)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Servicecontract  $servicecontract
     * @return \Illuminate\Http\Response
     */
    public function edit(Servicecontract $servicecontract)
    {
        return view('servicecontract.edit', compact(['servicecontract']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Servicecontract  $servicecontract
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Servicecontract $servicecontract)
    {
        $servicecontract->name = $request->get('name');
        $servicecontract->producer_name = $request->get('producer_name');
        $servicecontract->can_expire = $request->get('can_expire');

        $servicecontract->save();

        return redirect()->route('servicecontracts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Servicecontract  $servicecontract
     * @return \Illuminate\Http\Response
     */
    public function destroy(Servicecontract $servicecontract)
    {
        //
    }
}
