<?php

namespace App\Http\Controllers;

use App\Mail\WorkreportMail;
use App\Models\AdditionalMaterial;
use App\Models\Customer;
use App\Models\Document;
use App\Models\Workingtime;
use App\Models\Workreport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Log;
use Pdf;
use Storage;

class WorkreportController extends Controller
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
        $customer = Customer::find($request->get('uid'));

        if($customer) {
            if($request->get('aid') && $request->get('aid') > 0) {
                $workreport = Workreport::find($request->get('aid'));
                //$workreport->user_id = $request->get('userid');
                $workreport->editor_id = auth()->user()->id;
            } else {
                $workreport = new Workreport;
                //$workreport->user_id = $request->get('userid');
                $workreport->editor_id = auth()->user()->id;
            }

            $workreport->comment = $request->get('sonstiges');
            $workreport->work_finished = $request->boolean('arbeit-beendet');
            $workreport->date = \Carbon\Carbon::parse($request->get('arbeitsbericht-datum'));

            $workreport->additionalmaterials()->delete();
            if($request->get('zusatz-material')) {
                for($i = 0; $i < count($request->get('zusatz-material')); $i++) {
                    $am = new AdditionalMaterial;
                    $am->workreport_id = $workreport->id;
                    $am->designation = $request->get('zusatz-material')[$i];
                    $am->quantity = $request->get('zusatz-material-menge')[$i];
                    $am->save();
                }
            }

            if($request->get('unused-posnr')) {
                $workreport->notusedmaterials()->delete();
            }

            $workreport->workingtimes()->delete();
            if($request->get('arbeitszeit-datum')) {
                for($i=0;$i<count($request->get('arbeitszeit-datum')); $i++) {
                    $wt = new Workingtime;
                    $wt->workreport_id = $workreport->id;
                    $wt->date = \Carbon\Carbon::parse($request->get('arbeitszeit-datum')[$i]);
                    $wt->travel_time_from = \Carbon\Carbon::parse($request->get('arbeitszeit-reise-von')[$i]);
                    $wt->travel_time_to = \Carbon\Carbon::parse($request->get('arbeitszeit-reise-bis')[$i]);
                    $wt->work_from = \Carbon\Carbon::parse($request->get('arbeitszeit-arbeit-von')[$i]);
                    $wt->work_to = \Carbon\Carbon::parse($request->get('arbeitszeit-arbeit-bis')[$i]);
                    $wt->work_type = "";
                    // $wt->overtime = \Carbon\Carbon::parse($request->get('arbeitszeit-ueberstunden')[$i]);
                    $wt->text = $request->get('durchgefuehrte-arbeiten')[$i];
                    $wt->save();
                }
            }
            
            $workreport->save();

            $cu = Customer::where('id', $request->get('cid'))->first();
            if(!$cu->workreports->contains($workreport->id)) {
                $cu->workreports()->attach($workreport->id);
            }

            return response()->json();
            // return redirect()->route('arbeitsbericht', [$customer->id, $workreport->id]);
        }
        
        // return abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Workreport  $workreport
     * @return \Illuminate\Http\Response
     */
    public function show(Workreport $workreport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Workreport  $workreport
     * @return \Illuminate\Http\Response
     */
    public function edit(Workreport $workreport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Workreport  $workreport
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Workreport $workreport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Workreport  $workreport
     * @return \Illuminate\Http\Response
     */
    public function destroy(Workreport $workreport)
    {
        //
    }

    public function delete(Request $request) {
        $wr = Workreport::find($request->get('arbeitsbericht_id'));
        $wr->delete();
    }


    public function createpdf(Request $request) {
        // Log::debug($request->all());
        $this->store($request);


        $workreport = Workreport::find($request->get('arbeitsbericht_id'));
        $customer = $workreport->customer;



        $pdf = Pdf::loadView('workreportpdf', ["workreport" => $workreport, "customer" => $customer]);

        $content = $pdf->download()->getOriginalContent();

        $filename = substr(str_replace(' ', '', $customer->company), 0, 10) . '_' . $workreport->number . '.pdf';
        $filename = preg_replace("/[^A-Za-z0-9.\-]/", '', $filename);

        Storage::put('public/output/'.$filename, $content) ;

        // return $pdf->download(substr(str_replace(' ', '', $customer->company), 0, 10) . '_' . $workreport->number . '.pdf');

        if($workreport) {
            $workreport->locked = true;
            $workreport->save();
        }

        Mail::to($request->get('email'))
            ->bcc('ezuppa@pcs-muenchen.de')
            ->queue(new WorkreportMail($filename, $workreport));

        Mail::to($workreport->editor->email)
            ->queue(new WorkreportMail($filename, $workreport));

        return response()->json([]);
    }

    public function unlock(Request $request) {
        $workreport = Workreport::find($request->get('arbeitsbericht_id'));

        if($workreport) {
            $workreport->locked = false;
            $workreport->work_finished = false;
            $workreport->save();
        }

        return response()->json([]);
    }

    public function savesignature(Request $request) {
        $unterschrift = $request->get('unterschrift');
        $typ = $request->get('typ');
        $arbeitsberichtId = $request->get('arbeitsbericht_id');
        $nameAusgeschrieben = $request->get('name_ausgeschrieben');

        $data_pieces = explode(',', $unterschrift);
        $encoded_image = $data_pieces[1];
        $decoded_image = base64_decode($encoded_image);

        $upload_path = storage_path('app/public/upload/');

        file_put_contents($upload_path . 'unterschrift_' . $typ . '_' . $arbeitsberichtId . '.png', $decoded_image);

        $wr = Workreport::find($arbeitsberichtId);

        if($typ == "kundendienst") {
            $unterschrift = 'signature_customer_service';
            $name = 'name_customer_service';
        }

        if($typ == "kunde") {
            $unterschrift = 'signature_customer';
            $name = 'name_customer';
        }

        if($wr) {
            $wr->$unterschrift = true;
            $wr->$name = $nameAusgeschrieben;
            $wr->save();
        }

        return response()->json(['file' => asset('storage/upload/'.'unterschrift_' . $typ . '_' . $arbeitsberichtId . '.png')]);
    }



    public function upload(Request $request) {
        $filename = $request->file('files')->getClientOriginalName();
        $path = $request->file('files')->storeAs('public/upload', $filename);
        $url = Storage::url($path);

        // echo nl2br(print_r($request->all(),true));

        $workreport = Workreport::find($request->get('aid'));

        $document = new Document;
        $document->name = $filename;
        $document->path = $filename;
        $document->type = $request->get('type');
        $document->save();

        $workreport->documents()->attach($document);

        return redirect()->route('arbeitsbericht', [$request->get('uid'), $request->get('aid')]);
    }


    public function deletefile(Request $request) {
        $document = Document::find($request->get('documentId'));
        if($document) {
            $path = storage_path('app/public/upload/').$document->path;
            unlink($path);
            $document->delete();
        }

        // echo "ok";

        return response()->json(['message' => 'success']);
    }


}
