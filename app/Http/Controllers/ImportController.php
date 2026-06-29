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
