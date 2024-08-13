<?php

namespace App\Services;

use App\Http\Resources\OrderResource;
use Xendit\Invoice;
use App\Models\Order;
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
       $invoiceData->total_price = str_replace(['Rp.', '.'], '', $invoiceData->total_price);
    //    var_dump($invoiceData->total_price);
    //    exit;
        $api = new InvoiceApi();
        $create_invoice_request = new CreateInvoiceRequest([
            'external_id' => $invoiceData->external_id,
            'description' => 'Test Invoice',
            'amount' => $invoiceData->total_price,
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

    public function webHook(Request $request){
        $apiInstance = new InvoiceApi();
        $result = $apiInstance->getInvoiceById($request->id);        
        $order = Order::where('external_id', $result['external_id'])->firstorFail();
        if($order->status == 'settled'){
          return response()->json([
            'message' => 'Order already paid',
            'data' => $order
          ]);
        }else{
        $order->status = strtolower($result['status']);
        $order->save();
        }
        
    }
}
