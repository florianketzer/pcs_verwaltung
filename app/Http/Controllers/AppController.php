<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Material;
use App\Models\Servicecontract;
use App\Models\Workreport;
use Illuminate\Http\Request;
use DB;

class AppController extends Controller
{
    public function home(Request $request) {
        // $customers = Customer::all();

        // foreach($customers as $customer) {
        //     foreach($customer->user->workreports as $workreport) {
        //         if($customer->workreports->contains($workreport->id)) {
        //         } else {
        //             $customer->workreports()->attach($workreport->id);
        //         }
        //     }
        // }
        
        $result = null;
        return view('home', compact(['result']));
    }

    public function search(Request $request) {

        // $firma, $ansprechpartner, $kundennummer

        $customers = Customer::where(function ($query) use ($request) {
            if ($request->filled('suche-firma')) {
                $query->where('company', 'like', '%' . $request->get('suche-firma') . '%');
            }
            if ($request->filled('suche-ansprechpartner')) {
                $query->where('contact', 'like', '%' . $request->get('suche-ansprechpartner') . '%');
            }
            if ($request->filled('suche-kundennummer')) {
                $query->where('customer_id', 'like', '%' . $request->get('suche-kundennummer') . '%');
            }
        })
            ->orderBy('company', 'ASC')
            ->orderBy('contact', 'ASC');

        $sql = $customers->toSql(); // Get the SQL string

        $customers = $customers->get(); // Execute the query

        // up.company ASC, up.contact ASC

        $result = "-";
        return view('home', compact(['result', 'customers']));
    }

    public function arbeitsbericht (Request $request, Customer $customer, Workreport $workreport) {

        $user = $request->user();
        $materials = Material::all();
        
        if($workreport->id <= 0) {
            
            $last_workreport = Workreport::latest('number')->first();
            $nextnumber = $last_workreport->number+1;

            $workreport = new Workreport;
            $workreport->user_id = $customer->id;
            $workreport->editor_id = auth()->user()->id;
            $workreport->number = $nextnumber;
            $workreport->save();

            if(!$customer->workreports->contains($workreport->id)) {
                $customer->workreports()->attach($workreport->id);
            }

            return redirect()->route('arbeitsbericht', [$customer->id, $workreport->id]);
        }

        return view('arbeitsbericht', compact(['user', 'customer', 'workreport', 'materials']));
    }

    public function customer(Request $request, Customer $customer) {
        $servicecontracts = Servicecontract::all();
        return view('customer', compact(['customer', 'servicecontracts']));
    }
}
