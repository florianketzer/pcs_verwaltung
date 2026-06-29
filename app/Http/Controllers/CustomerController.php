<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Models\Servicecontract;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'kundennummer' => 'unique:users,username',
            // 'email' => 'unique:users,email',
        ]);

        $user = new User;
        $user->username = $request->get('kundennummer');
        $user->email = $request->get('email');
        $user->password = "";
        $user->save();

        $customer = new Customer;
        $customer->customer_id = $request->get('kundennummer');
        $customer->company = $request->get('firma');
        $customer->contact = $request->get('ansprechpartner');
        $customer->servicetype_id = $request->get('servicetyp');
        $customer->street = $request->get('strasse');
        $customer->addition = $request->get('zusatz');
        $customer->postcode = $request->get('plz');
        $customer->city = $request->get('ort');
        $customer->telephone = $request->get('telefon');
        $customer->mobile = $request->get('mobil');
        $customer->comment_intern = $request->get('kommentar');
        $customer->user_id = $user->id;
        $customer->save();

        if($request->get('servicecontracts')) {
            foreach($request->get('servicecontracts') as $key => $value) {
                if(key_exists('sc', $value)) {
                    if(key_exists('expire_at', $value)) {
                        $customer->servicecontracts()->attach(Servicecontract::find($value['sc']), ['expire_at' => \Carbon\Carbon::parse($value['expire_at'])]);
                    } else {
                        $customer->servicecontracts()->attach(Servicecontract::find($value['sc']));
                    }
                }
            }
        }

        if($request->get('save_new') && $request->get('save_new') == "1") {
            return redirect()->route('arbeitsbericht', $customer->id);
        }

        return redirect()->route('customer', $customer->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        foreach($customer->servicecontracts as $sc) {
            $customer->servicecontracts()->detach($sc);
        }

        if($request->get('servicecontracts')) {
            foreach($request->get('servicecontracts') as $key => $value) {
                if(key_exists('sc', $value)) {
                    if(key_exists('expire_at', $value)) {
                        $customer->servicecontracts()->attach(Servicecontract::find($value['sc']), ['expire_at' => \Carbon\Carbon::parse($value['expire_at'])]);
                    } else {
                        $customer->servicecontracts()->attach(Servicecontract::find($value['sc']));
                    }
                }
            }
        }

        $user = $customer->user;
        $user->email = $request->get('email');
        $user->save();

        $customer->customer_id = $request->get('kundennummer');
        $customer->company = $request->get('firma');
        $customer->contact = $request->get('ansprechpartner');
        $customer->servicetype_id = $request->get('servicetyp');
        $customer->street = $request->get('strasse');
        $customer->addition = $request->get('zusatz');
        $customer->postcode = $request->get('plz');
        $customer->city = $request->get('ort');
        $customer->telephone = $request->get('telefon');
        $customer->mobile = $request->get('mobil');
        $customer->comment_intern = $request->get('kommentar');
        $customer->save();

        if($request->get('save_new') && $request->get('save_new') == "1") {
            return redirect()->route('arbeitsbericht', $customer->id);
        }

        return redirect()->route('customer', $customer->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
