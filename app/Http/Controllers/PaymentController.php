<?php

namespace App\Http\Controllers;

use Xendit\Configuration;
use Illuminate\Http\Request;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;

class PaymentController extends Controller
{
    public function __construct()
    {
       Configuration::setXenditKey("xnd_development_ATQsiViNZsT2t9SlNMiNUQRpZP16X2nSnRwfmqD1PGFR6DpErOghC9etR3gNfAbj");
    }


    public function create(){
        $api = new InvoiceApi();
        $create_invoice_request = new CreateInvoiceRequest([
            'external_id' => 'test1234',
            'description' => 'Test Invoice',
            'amount' => 10000,
            'invoice_duration' => 172800,
            'currency' => 'IDR',
            'reminder_time' => 1
            ]);

            try{
                $result = $api->createInvoice($create_invoice_request);
                return response()->json($result);
            }catch(\Exception $e){
                return response()->json($e->getMessage());
            }
    }
}
