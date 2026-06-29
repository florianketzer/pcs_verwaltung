<?php

namespace App\Jobs;

use App\Mail\ExpiredServiceContractMail;
use App\Models\Customer;
use App\Models\Servicecontract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;
use DB;
use Log;

class SendExpiredServiceContractEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // DB::enableQueryLog();
        // $contracts = Customer::where('servicecontracts', function($query){
        //     $query->whereNotNull('expire_at')->where('expire_at', '<', \Carbon\Carbon::now());
        // });

        $contracts = DB::table('customer_servicecontract')
            ->join('customers', 'customer_servicecontract.customer_id', '=', 'customers.id')
            ->whereNotNull('expire_at')
            ->where('expire_at', '<', now())
            ->select('customer_servicecontract.*', 'customers.company as customer_company')
            ->get();
        // Log::debug(DB::getQueryLog());

        Log::debug($contracts);

        $email = new ExpiredServiceContractMail($contracts);
        Mail::to('f.ketzer@kr-vision.de')->send($email);
    }
}
