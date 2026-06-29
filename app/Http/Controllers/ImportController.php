<?php

namespace App\Http\Controllers;

use App\Imports\ServicecontractImport;
use App\Models\AdditionalMaterial;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Document;
use App\Models\Download;
use App\Models\Material;
use App\Models\NotUsedMaterial;
use App\Models\Permission;
use App\Models\Producer;
use App\Models\Servicecontract;
use App\Models\Servicetype;
use App\Models\System;
use App\Models\User;
use App\Models\Usergroup;
use App\Models\Userlog;
use App\Models\Workingtime;
use App\Models\Workreport;
use Illuminate\Http\Request;
use DB;
use Str;
use Hash;
use Excel;

class ImportController extends Controller
{
    public function import(Request $request)
    {

        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // \App\Models\Customer::truncate();
        // \App\Models\Servicetype::truncate();
        // \App\Models\Workreport::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        //Permissions
        $live_permissions = DB::connection('mysql_live')->select('SELECT * FROM permission');
        foreach ($live_permissions as $live_permission) {
            $check = Permission::where('name', $live_permission->name)->first();
            if (!$check) {
                $permission = new Permission;
                $permission->name = $live_permission->name;
                $permission->save();
            }
        }


        //usergroups
        $live_usergroups = DB::connection('mysql_live')->select('SELECT * FROM usergroup');
        foreach ($live_usergroups as $live_usergroup) {
            $check = Usergroup::where('name', $live_usergroup->name)->first();
            if (!$check) {
                $usergroup = new Usergroup();
                $usergroup->name = $live_usergroup->name;
                $usergroup->save();
            }
        }

        $live_usergroup_permissions = DB::connection('mysql_live')->select('SELECT * FROM usergroup_permission');
        foreach ($live_usergroup_permissions as $live_usergroup_permission) {
            DB::table('usergroup_permission')->insert([
                'usergroup_id' => $live_usergroup_permission->usergroup_id,
                'permission_id' => $live_usergroup_permission->permission_id,
            ]);
        }

        // User Import
        $live_users = DB::connection('mysql_live')->select("SELECT * FROM user");

        foreach ($live_users as $live_user) {
            $check = User::where('id', $live_user->id)->first();
            if (!$check) {
                $tmp = User::where('email', $live_user->email)->first();
                $email = "";

                if ($tmp) {
                    $email = Str::random(4) . "_" . $live_user->email;
                } else {
                    $email = $live_user->email;
                }

                $user = User::factory()->create([
                    'id' => $live_user->id,
                    'username' => $live_user->username,
                    'email' => $email,
                    'name' => '',
                    'password' => $live_user->username == "happypixel" ? Hash::make('gaudi123') : ''
                ]);
            }
        }

        $live_user_usergroups = DB::connection('mysql_live')->select('SELECT * FROM usertousergroup');
        foreach ($live_user_usergroups as $live_user_usergroup) {
            $check = User::find($live_user_usergroup->user_id);
            if ($check) {
                DB::table('user_usergroup')->insert([
                    'usergroup_id' => $live_user_usergroup->usergroup_id,
                    'user_id' => $live_user_usergroup->user_id,
                ]);
            }
        }

        // Service Types
        $live_servicetypes = DB::connection('mysql_live')->select('SELECT * FROM servicetype');
        foreach ($live_servicetypes as $live_servicetype) {
            $check = Servicetype::where('name', $live_servicetype->name)->first();
            if (!$check) {
                $servicetype = new Servicetype;
                $servicetype->name = $live_servicetype->name;
                $servicetype->save();
            }
        }

        $sql = "SELECT * FROM document";
        $live_documents = DB::connection('mysql_live')->select($sql);

        foreach ($live_documents as $live_document) {

            $test_document = Document::find($live_document->id);

            if (!$test_document) {
                $document = new Document;
                $document->id = $live_document->id;
                $document->name = $live_document->name;
                $document->path = $live_document->pfad;
                $document->type = $live_document->typ;
                $document->save();
            }
        }

        // Customer
        $live_customers = DB::connection('mysql_live')->select('SELECT * FROM userprofil');

        foreach ($live_customers as $live_customer) {
            $check = Customer::where('customer_id', $live_customer->customer_id)->first();
            if (!$check) {

                $tmp_user = User::find($live_customer->user_id);

                $customer = new Customer;
                $customer->id = $live_customer->user_id;
                $customer->company = $live_customer->company;
                $customer->contact = $live_customer->contact;
                $customer->street = $live_customer->street;
                $customer->addition = $live_customer->zusatz;
                $customer->postcode = $live_customer->postcode;
                $customer->city = $live_customer->city;
                $customer->customer_id = $live_customer->customer_id;
                $customer->telephone = $live_customer->telephone;
                $customer->mobile = $live_customer->mobile;
                if ($live_customer->servicetype_id != 0) $customer->servicetype_id = $live_customer->servicetype_id;
                $customer->comment_intern = $live_customer->comment_intern;
                $customer->user_id = $tmp_user ? $live_customer->user_id : NULL;
                $customer->save();
            }
        }

        $sql = "SELECT * FROM arbeitsbericht WHERE modified>DATE_SUB(NOW(), INTERVAL 2 YEAR) ORDER BY id";
        // $sql = "SELECT * FROM arbeitsbericht ORDER BY id";
        $live_arbeitsberichte = DB::connection('mysql_live')->select($sql);

        foreach ($live_arbeitsberichte as $live_arbeitsbericht) {
            if ($live_arbeitsbericht->user_id > 0 && $live_arbeitsbericht->bearbeiter_id > 0) {
                $test_user = User::find($live_arbeitsbericht->user_id);
                $test_editor = User::find($live_arbeitsbericht->bearbeiter_id);
                $test_workreport = Workreport::find($live_arbeitsbericht->id);
                $test_doc1 = Document::find($live_arbeitsbericht->lieferschein_id);
                $test_doc2 = Document::find($live_arbeitsbericht->zusatzdokument_id);

                if ($test_user && $test_editor && !$test_workreport) {
                    $workreport = new Workreport;
                    $workreport->id = $live_arbeitsbericht->id;
                    $workreport->number = $live_arbeitsbericht->nummer != '' ? $live_arbeitsbericht->nummer : NULL;
                    $workreport->editor_id = $live_arbeitsbericht->bearbeiter_id;
                    $workreport->user_id = $live_arbeitsbericht->user_id;
                    $workreport->comment = $live_arbeitsbericht->kommentar;
                    $workreport->work_finished = $live_arbeitsbericht->arbeit_beendet;
                    $workreport->signature_customer_service = $live_arbeitsbericht->unterschrift_kundendienst;
                    $workreport->name_customer_service = $live_arbeitsbericht->name_kundendienst_ausgeschrieben;
                    $workreport->signature_customer = $live_arbeitsbericht->unterschrift_kunde;
                    $workreport->name_customer = $live_arbeitsbericht->name_kunde_ausgeschrieben;
                    $workreport->date = $live_arbeitsbericht->datum && $live_arbeitsbericht->datum != "0000-00-00 00:00:00" ? \Carbon\Carbon::parse($live_arbeitsbericht->datum) : NULL;
                    $workreport->locked = $live_arbeitsbericht->locked;
                    $workreport->updated_at = $live_arbeitsbericht->modified;
                    $workreport->delivery_bill_id = $test_doc1 ? $live_arbeitsbericht->lieferschein_id : NULL;
                    $workreport->document_id = $test_doc2 ? $live_arbeitsbericht->zusatzdokument_id : NULL;
                    $workreport->save();

                    // if($live_arbeitsbericht->id == 11051) {
                    //     echo $workreport."<br><br>";
                    // }
                }

                // if($live_arbeitsbericht->id == 11051) {
                //     echo $test_user."<br>";
                //     echo $test_editor."<br>";
                //     echo $test_workreport."<br>";
                //     echo "HIER<br>";
                //     dd();
                // }
            }
        }



        $sql = "SELECT * FROM usertodocument";
        $live_connections = DB::connection('mysql_live')->select($sql);

        foreach ($live_connections as $live_connection) {
            $check = User::find($live_connection->user_id);
            if ($check) {
                $db = DB::table('document_user')->insert([
                    'document_id' => $live_connection->document_id,
                    'user_id' => $live_connection->user_id
                ]);
            }
        }

        $sql = "SELECT * FROM nichtverwendetes_material";
        $live_materials2 = DB::connection('mysql_live')->select($sql);

        foreach ($live_materials2 as $live_material) {

            $test_material = NotUsedMaterial::find($live_material->id);
            $test_workreport = Workreport::find($live_material->arbeitsbericht_id);

            if (!$test_material && $test_workreport) {
                $material = new NotUsedMaterial;
                $material->id = $live_material->id;
                $material->workreport_id = $live_material->arbeitsbericht_id;
                $material->posnr = $live_material->posnr;
                $material->quantity = $live_material->menge;
                $material->save();
            }
        }

        $sql = "SELECT * FROM material";
        $live_materials = DB::connection('mysql_live')->select($sql);

        foreach ($live_materials as $live_material) {

            $test_material = Material::find($live_material->id);

            if (!$test_material) {
                $material = new Material;
                $material->id = $live_material->id;
                $material->name = $live_material->name;
                $material->save();
            }
        }

        $sql = "SELECT * FROM producer";
        $live_producers = DB::connection('mysql_live')->select($sql);

        foreach ($live_producers as $live_producer) {

            $test_producer = Producer::find($live_producer->id);

            if (!$test_producer) {
                $producer = new Producer;
                $producer->id = $live_producer->id;
                $producer->name = $live_producer->name;
                $producer->save();
            }
        }

        $sql = "SELECT * FROM category";
        $live_categories = DB::connection('mysql_live')->select($sql);

        foreach ($live_categories as $live_category) {

            $test_category = Category::find($live_category->id);

            if (!$test_category) {
                $category = new Category;
                $category->id = $live_category->id;
                $category->name = $live_category->name;
                $category->save();
            }
        }

        $sql = "SELECT * FROM system";
        $live_systems = DB::connection('mysql_live')->select($sql);

        foreach ($live_systems as $live_system) {

            $test_system = System::find($live_system->id);

            if (!$test_system) {
                $system = new System;
                $system->id = $live_system->id;
                $system->producer_id = $live_system->producer_id;
                $system->name = $live_system->name;
                $system->save();
            }
        }

        $sql = "SELECT * FROM user_log";
        $live_logs = DB::connection('mysql_live')->select($sql);

        foreach ($live_logs as $live_log) {

            $test_log = Userlog::find($live_log->id);
            $test_user = User::find($live_log->user_id);

            if (!$test_log && $test_user) {
                $userlog = new Userlog;
                $userlog->id = $live_log->id;
                $userlog->user_id = $live_log->user_id;
                $userlog->message = $live_log->message;
                $userlog->extrainfos = $live_log->extrainfos;
                $userlog->save();
            }
        }

        $sql = "SELECT * FROM download";
        $live_downloads = DB::connection('mysql_live')->select($sql);

        foreach ($live_downloads as $live_download) {

            $test_download = Download::find($live_download->id);

            if (!$test_download) {
                $download = new Download;
                $download->id = $live_download->id;
                $download->system_id = $live_download->system_id;
                $download->category_id = $live_download->category_id;
                $download->title = $live_download->title;
                $download->file = $live_download->file;
                $download->filesize = $live_download->filesize;
                $download->save();
            }
        }

        $sql = "SELECT * FROM berichttodocument";
        $live_connections = DB::connection('mysql_live')->select($sql);

        foreach ($live_connections as $live_connection) {
            $check = Document::find($live_connection->document_id);
            if ($check) {

                $test_document = Document::find($live_connection->document_id);
                $test_workreport = Workreport::find($live_connection->bericht_id);

                if ($test_document && $test_workreport) {
                    $db = DB::table('document_workreport')->insert([
                        'document_id' => $live_connection->document_id,
                        'workreport_id' => $live_connection->bericht_id
                    ]);
                }
            }
        }

        $sql = "SELECT * FROM arbeitszeiten";
        $live_arbeitszeiten = DB::connection('mysql_live')->select($sql);

        foreach ($live_arbeitszeiten as $live_arbeitszeit) {

            $test_workreport = Workreport::find($live_arbeitszeit->arbeitsbericht_id);
            $test_worktime = Workingtime::find($live_arbeitszeit->id);

            if ($test_workreport && !$test_worktime) {
                $workingtime = new Workingtime;
                $workingtime->id = $live_arbeitszeit->id;
                $workingtime->workreport_id = $live_arbeitszeit->arbeitsbericht_id;
                $workingtime->date = \Carbon\Carbon::parse($live_arbeitszeit->datum);
                $workingtime->travel_time_from = $live_arbeitszeit->reise_von;
                $workingtime->travel_time_to = $live_arbeitszeit->reise_bis;
                $workingtime->work_from = $live_arbeitszeit->arbeit_von;
                $workingtime->work_to = $live_arbeitszeit->arbeit_bis;
                $workingtime->work_type = $live_arbeitszeit->arbeitsart;
                $workingtime->overtime = $live_arbeitszeit->ueberstunden;
                $workingtime->text = $live_arbeitszeit->text;
                $workingtime->save();
            }
        }

        $sql = "SELECT * FROM zusatzmaterial";
        $live_zusatzmateriall = DB::connection('mysql_live')->select($sql);

        foreach ($live_zusatzmateriall as $live_zusatzmaterial) {

            $test_workreport = Workreport::find($live_zusatzmaterial->arbeitsbericht_id);
            $test_additionalmaterial = AdditionalMaterial::find($live_zusatzmaterial->id);

            if ($test_workreport && !$test_additionalmaterial) {
                $additionalmaterial = new AdditionalMaterial;
                $additionalmaterial->id = $live_zusatzmaterial->id;
                $additionalmaterial->workreport_id = $live_zusatzmaterial->arbeitsbericht_id;
                $additionalmaterial->designation = $live_zusatzmaterial->bezeichnung;
                $additionalmaterial->quantity = $live_zusatzmaterial->menge;
                $additionalmaterial->save();
            }
        }

        return "---";

        // $customers = Customer::all();

        // foreach($customers as $customer) {
        //     if($customer->user_id>0 && $customer->workreports->count()==0) {
        //         $sql = "SELECT * FROM arbeitsbericht WHERE user_id=".$customer->user_id." AND modified>DATE_SUB(NOW(), INTERVAL 4 YEAR)";
        //         $live_arbeitsberichte = DB::connection('mysql_live')->select($sql);

        //         foreach($live_arbeitsberichte as $live_arbeitsbericht) {
        //             $workreport = new Workreport;
        //             $workreport->number = $live_arbeitsbericht->nummer != '' ? $live_arbeitsbericht->nummer : NULL;
        //             $workreport->customer_id = $customer->id;
        //             $workreport->user_id = $live_arbeitsbericht->user_id;
        //             $workreport->comment = $live_arbeitsbericht->kommentar;
        //             $workreport->work_finished = $live_arbeitsbericht->arbeit_beendet;
        //             $workreport->signature_customer_service = $live_arbeitsbericht->unterschrift_kundendienst;
        //             $workreport->name_customer_service = $live_arbeitsbericht->name_kundendienst_ausgeschrieben;
        //             $workreport->signature_customer = $live_arbeitsbericht->unterschrift_kunde;
        //             $workreport->name_customer = $live_arbeitsbericht->name_kunde_ausgeschrieben;
        //             $workreport->date = $live_arbeitsbericht->datum && $live_arbeitsbericht->datum != "0000-00-00 00:00:00" ? \Carbon\Carbon::parse($live_arbeitsbericht->datum) : NULL;
        //             $workreport->locked = $live_arbeitsbericht->locked;
        //             // $workreport->delivery_bill_id = $arbeitsbericht->lieferschein_id;
        //             // $workreport->document_id = $arbeitsbericht->zusatzdokument_id;
        //             $workreport->save();
        //         }
        //     }
        // }

        return "ENDE";

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // \App\Models\Customer::truncate();
        // \App\Models\Servicetype::truncate();
        // \App\Models\Workreport::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // $servicetypes = DB::connection('mysql_live')->select('SELECT * FROM servicetype');

        // foreach($servicetypes as $service) {
        //     $ser = new Servicetype;
        //     $ser->name = $service->name;
        //     $ser->save();
        // }

        // $customers = DB::connection('mysql_live')->select('SELECT * FROM userprofil');

        // foreach($customers as $customer) {
        //     $cust = new Customer;
        //     $cust->company = $customer->company;
        //     $cust->contact = $customer->contact;
        //     $cust->street = $customer->street;
        //     $cust->addition = $customer->zusatz;
        //     $cust->postcode = $customer->postcode;
        //     $cust->city = $customer->city;
        //     $cust->customer_id = $customer->customer_id;
        //     $cust->telephone = $customer->telephone;
        //     $cust->mobile = $customer->mobile;
        //     if( $customer->servicetype_id != 0 ) $cust->servicetype_id = $customer->servicetype_id;
        //     $cust->comment_intern = $customer->comment_intern;
        //     $cust->tmp_user_id = $customer->user_id;
        //     $cust->save();

        // }

        $customers = Customer::all();

        foreach ($customers as $customer) {
            // echo $customer->workreports->count()."<br>";
            if ($customer->workreports->count() == 0) {
                // echo "SELECT * FROM arbeitsbericht WHERE user_id=".$customer->tmp_user_id." AND modified>DATE_SUB(NOW(), INTERVAL 2 YEAR)<br>";
                $arbeitsberichte = DB::connection('mysql_live')->select("SELECT * FROM arbeitsbericht WHERE user_id=" . $customer->tmp_user_id . " AND modified>DATE_SUB(NOW(), INTERVAL 2 YEAR)");

                foreach ($arbeitsberichte as $arbeitsbericht) {
                    // echo "DA<br>";
                    // echo $arbeitsbericht->datum."<br>";
                    // echo "id: ".$arbeitsbericht->id."<br>";
                    $workreport = new Workreport;
                    $workreport->number = $arbeitsbericht->nummer != '' ? $arbeitsbericht->nummer : NULL;
                    $workreport->customer_id = $customer->id;
                    $workreport->user_id = 1;
                    $workreport->comment = $arbeitsbericht->kommentar;
                    $workreport->work_finished = $arbeitsbericht->arbeit_beendet;
                    $workreport->signature_customer_service = $arbeitsbericht->unterschrift_kundendienst;
                    $workreport->name_customer_service = $arbeitsbericht->name_kundendienst_ausgeschrieben;
                    $workreport->signature_customer = $arbeitsbericht->unterschrift_kunde;
                    $workreport->name_customer = $arbeitsbericht->name_kunde_ausgeschrieben;
                    $workreport->date = $arbeitsbericht->datum && $arbeitsbericht->datum != "0000-00-00 00:00:00" ? \Carbon\Carbon::parse($arbeitsbericht->datum) : NULL;
                    $workreport->locked = $arbeitsbericht->locked;
                    // $workreport->delivery_bill_id = $arbeitsbericht->lieferschein_id;
                    // $workreport->document_id = $arbeitsbericht->zusatzdokument_id;
                    $workreport->save();
                }
            }
        }

        return "okay";
    }





    public function importcsv(Request $request)
    {
        return view('importcsv');
    }

    public function runimportcsv(Request $request)
    {
        $data = Excel::toArray(new ServicecontractImport, $request->file('csv'))[0];
        $servicecontracts = Servicecontract::all();

        $current_row = 0;
        $errors = "";

        foreach ($data as $d) {
            if ($d[0] != "user_id") {

                $customer = Customer::where('user_id', $d[0])->first();

                if ($customer) {
                    if ($d[5] != "") {
                        $sc = Servicecontract::where('name', 'ARN2')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }

                    if ($d[6] != "") {
                        $sc = Servicecontract::where('name', 'ARN8')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }

                    if ($d[7] != "") {
                        $sc = Servicecontract::where('name', 'ARO2')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }

                    if ($d[8] != "") {
                        $sc = Servicecontract::where('name', 'ARO8')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }

                    if ($d[9] != "") {
                        $sc = Servicecontract::where('name', 'BRN2')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }

                    if ($d[10] != "") {
                        $sc = Servicecontract::where('name', 'BRN8')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }

                    if ($d[11] != "") {
                        $sc = Servicecontract::where('name', 'BRO2')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }

                    if ($d[12] != "") {
                        $sc = Servicecontract::where('name', 'BRO8')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }

                    if ($d[13] != "") {
                        $sc = Servicecontract::where('name', 'BRO8 oE')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }

                    if ($d[14] != "") {
                        $sc = Servicecontract::where('name', 'LRN2')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }

                    if ($d[15] != "") {
                        $sc = Servicecontract::where('name', 'LRN8')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }

                    if ($d[16] != "") {
                        $sc = Servicecontract::where('name', 'MRN2')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }

                    if ($d[17] != "") {
                        $sc = Servicecontract::where('name', 'MRN8')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }

                    if ($d[18] != "") {
                        $sc = Servicecontract::where('name', 'PRN2')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }

                    if ($d[19] != "") {
                        $sc = Servicecontract::where('name', 'PRN8')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }

                    if ($d[20] != "") {
                        $sc = Servicecontract::where('name', 'PRO2')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }

                    if ($d[21] != "") {
                        $sc = Servicecontract::where('name', 'PRO8')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }

                    if ($d[22] != "") {
                        $sc = Servicecontract::where('name', 'RNN8')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }

                    if ($d[23] != "") {
                        $sc = Servicecontract::where('name', 'SRN8')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }

                    if ($d[24] != "") {
                        $sc = Servicecontract::where('name', 'SRN4')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc);
                            }
                        }
                    }





                    if ($d[25] != "") {
                        $sc = Servicecontract::where('name', 'Avaya IPOSS - IP Office')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc, ['expire_at' => \Carbon\Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($d[26])->format('d.m.Y'))]);
                            }
                        }
                    }

                    if ($d[27] != "") {
                        $sc = Servicecontract::where('name', 'Avaya IPOSS - Server Edition')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc, ['expire_at' => \Carbon\Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($d[28])->format('d.m.Y'))]);
                            }
                        }
                    }

                    if ($d[29] != "") {
                        $sc = Servicecontract::where('name', 'Avaya IPOSS - ASBCE')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc, ['expire_at' => \Carbon\Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($d[30])->format('d.m.Y'))]);
                            }
                        }
                    }

                    if ($d[31] != "") {
                        $sc = Servicecontract::where('name', 'Avaya IPOSS - CIE')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc, ['expire_at' => \Carbon\Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($d[32])->format('d.m.Y'))]);
                            }
                        }
                    }

                    if ($d[33] != "") {
                        $sc = Servicecontract::where('name', 'Innovaphone SSA')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc, ['expire_at' => \Carbon\Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($d[34])->format('d.m.Y'))]);
                            }
                        }
                    }

                    if ($d[35] != "") {
                        $sc = Servicecontract::where('name', 'Audiocodes')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc, ['expire_at' => \Carbon\Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($d[36])->format('d.m.Y'))]);
                            }
                        }
                    }

                    if ($d[37] != "") {
                        $sc = Servicecontract::where('name', 'Estos ProCall')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc, ['expire_at' => \Carbon\Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($d[38])->format('d.m.Y'))]);
                            }
                        }
                    }

                    if ($d[39] != "") {
                        $sc = Servicecontract::where('name', 'Estos MetaDirectory')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc, ['expire_at' => \Carbon\Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($d[40])->format('d.m.Y'))]);
                            }
                        }
                    }

                    if ($d[41] != "") {
                        // echo $d[42]."<br>";
                        // // echo \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(intval($d[42]))."<br>";
                        // echo \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(intval($d[42])))."<br>";
                        // echo intval($d[42])."<br>";
                        // echo \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(intval($d[42])));
                        // die();
                        $sc = Servicecontract::where('name', 'Estos Mobility Service')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc, ['expire_at' => \Carbon\Carbon::parse(\Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(intval($d[42]))))->format('Y-m-d')]);
                            }
                        }
                    }

                    if ($d[43] != "") {
                        $sc = Servicecontract::where('name', 'Estos Meetings')->first();
                        if ($sc) {
                            if(!$customer->servicecontracts->contains($sc->id)) {
                                $customer->servicecontracts()->attach($sc, ['expire_at' => \Carbon\Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($d[44])->format('d.m.Y'))]);
                            }
                        }
                    }
                }
            }
        }
    }
}
