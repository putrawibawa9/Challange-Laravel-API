<?php

namespace App\Services;

use Xendit\Configuration;
use Illuminate\Http\Request;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;

class PaymentService
{

     public function __construct()
    {
       Configuration::setXenditKey("xnd_development_ATQsiViNZsT2t9SlNMiNUQRpZP16X2nSnRwfmqD1PGFR6DpErOghC9etR3gNfAbj");
    }
   public function createInvoice($invoiceData){
    $invoiceData = str_replace(['Rp.', '.'], '', $invoiceData);
        $api = new InvoiceApi();
        $create_invoice_request = new CreateInvoiceRequest([
            'external_id' => '123',
            'description' => 'Test Invoice',
            'amount' => $invoiceData,
            'invoice_duration' => 172800,
            'currency' => 'IDR',
            'reminder_time' => 1
            ]);

            try{
                $result = $api->createInvoice($create_invoice_request);
                return $result;
            }catch(\Exception $e){
                return response()->json($e->getMessage());
            }
    }
}
