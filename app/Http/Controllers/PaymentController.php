<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Paystack;
use Flash;
use App\Models\Transaction;
use App\Models\AccountHistory;
use App\Models\Account;
use App\Models\Qrcode as QrcodeModel;
use App\Models\User;
use Auth;

class PaymentController extends Controller
{

    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway()
    {
        return Paystack::getAuthorizationUrl()->redirectNow();
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();
       //dd($paymentDetails);

        if($paymentDetails['status'] != 'success'){
            Flash::error('Sorry, payment failed');

            //redirect here
            return redirect()->route('qrcodes.show',['id' => $paymentDetails['metadata']['qrcode_id'] ]);
        }

        //check if the amount paid is same as amount they are supposed to pay
        $qrcode = QrcodeModel::find($paymentDetails['metadata']['qrcode_id']);

        if($qrcode->amount != ($paymentDetails['amount'])/100){
            Flash::error('Sorry, you are not that smart');
            return redirect()->route('qrcodes.show',['id' => $paymentDetails['metadata']['qrcode_id'] ]);
        }

        //update transaction
        $transaction = Transaction::where('id',$paymentDetails['metadata']['transaction_id'] )->first();

        Transaction::where('id', $paymentDetails['metadata']['transaction_id'])
        ->update(['status' => 'completed']);
         

        //get buyer details
        $buyer = User::find($paymentDetails['metadata']['buyer_user_id']);
        

        //update qrcode owner account 
        $qrCoderOwnerAccount = Account::where('user_id', $qrcode->user_id)->first();

        Account::where('user_id', $qrcode->user_id)->update([
            'balance' => ($qrCoderOwnerAccount->balance + $qrcode->amount),
            'total_credit' => ($qrCoderOwnerAccount->total_credit + $qrcode->amount)
        ]);

        //creating account history of qrcode owner
        AccountHistory::create([
            'user_id' => $qrcode->user_id,
            'account_id' => $qrCoderOwnerAccount->id,
            'message' => 'Received '.$transaction->payment_method.' payment from'.$buyer->email .'for qrcode: '.$qrcode->product_name
        ]);


        //update buyer account and account histroy
        $buyerAccount = Account::where('user_id', $paymentDetails['metadata']['buyer_user_id'])->first();

        Account::where('user_id', $paymentDetails['metadata']['buyer_user_id'])->update([
            'total_debit' => ($qrCoderOwnerAccount->total_credit + $qrcode->amount)
        ]);

        //creating account history of qrcode owner
        AccountHistory::create([
            'user_id' => $paymentDetails['metadata']['buyer_user_id'],
            'account_id' => $buyerAccount->id,
            'message' => 'Paid '.$transaction->payment_method.' payment to'.$qrcode->user['email'] .'for qrcode: '.$qrcode->product_name
        ]);
        flash::success('Payment successfully');
        return redirect(route('transactions.show' ,['id'=> $transaction->id]));



    //     //send email alert to both parties
    //     // Qrcode owner email : $qrcode->user['email]
    //     // Buyer email : $paymentDetails['metadata']['buyer_user_id']

        
     }
}