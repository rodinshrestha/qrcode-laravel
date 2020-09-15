<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateQrcodeRequest;
use App\Http\Requests\UpdateQrcodeRequest;
use App\Repositories\QrcodeRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;
use QRCode;
use Auth;
use App\Models\Qrcode as QrcodeModel;
use App\Models\User;
use App\Models\Transaction;

class QrcodeController extends AppBaseController
{
    /** @var  QrcodeRepository */
    private $qrcodeRepository;

    public function __construct(QrcodeRepository $qrcodeRepo)
    {
        $this->qrcodeRepository = $qrcodeRepo;
    }

    /**
     * Display a listing of the Qrcode.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
       

        //only admin should view all 
        if(Auth::user()->role_id < 3){

            $qrcodes = $this->qrcodeRepository->all();
        
            
        }else{
            $qrcodes = QrcodeModel::where('user_id', Auth::user()->id)->get();
        }



        return view('qrcodes.index')
            ->with('qrcodes', $qrcodes);
    }

    /**
     * Show the form for creating a new Qrcode.
     *
     * @return Response
     */
    public function create()
    {
        return view('qrcodes.create');
    }

    /**
     * Store a newly created Qrcode in storage.
     *
     * @param CreateQrcodeRequest $request
     *
     * @return Response
     */
    public function store(CreateQrcodeRequest $request)
    {
        $input = $request->all();

        //save data to the database
        $qrcode = $this->qrcodeRepository->create($input);

    
        //generate qrcode
        //save qrcode image in our folder on this site
        $file = 'Generated_qrcode/'.$qrcode->product_name.'.png';

        $newQrcode = QRCode::text("fuck u bitch")
                    ->setSize(8)
                    ->setMargin(2)
                    ->setOutfile($file)
                    ->png();

                  

            $input['qrcode_path'] = $file;

            //update database
            $newQRCode = QrcodeModel::where('id', $qrcode->id)->update(['qrcode_path'=> $input['qrcode_path']]);

        
            

          if($newQRCode){ 

        Flash::success('Qrcode saved successfully.');

        }else{

            Flash::error('Failed to save QRCodes.');
        }


       return redirect(route('qrcodes.show',['id' => $qrcode->id]));
      // return view('qrcodes.show')->with('qrcode', $qrcode);
    }

    /**
     * Display the specified Qrcode.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $qrcode = $this->qrcodeRepository->find($id);

        if (empty($qrcode)) {
            Flash::error('Qrcode not found');

            return redirect(route('qrcodes.index'));
        }

        $transactions = $qrcode->transactions;

        return view('qrcodes.show')->with('qrcode', $qrcode)->with('transactions', $transactions);
    }

    /**
     * Show the form for editing the specified Qrcode.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $qrcode = $this->qrcodeRepository->find($id);

        if (empty($qrcode)) {
            Flash::error('Qrcode not found');

            return redirect(route('qrcodes.index'));
        }

        return view('qrcodes.edit')->with('qrcode', $qrcode);
    }

    /**
     * Update the specified Qrcode in storage.
     *
     * @param int $id
     * @param UpdateQrcodeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateQrcodeRequest $request)
    {
        $qrcode = $this->qrcodeRepository->find($id);

        if (empty($qrcode)) {
            Flash::error('Qrcode not found');

            return redirect(route('qrcodes.index'));
        }

        $qrcode = $this->qrcodeRepository->update($request->all(), $id);

        Flash::success('Qrcode updated successfully.');

        return view('qrcodes.show')->with('qrcode', $qrcode);
    }

    /**
     * Remove the specified Qrcode from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $qrcode = $this->qrcodeRepository->find($id);

        if (empty($qrcode)) {
            Flash::error('Qrcode not found');

            return redirect(route('qrcodes.index'));
        }

        $this->qrcodeRepository->delete($id);

        Flash::success('Qrcode deleted successfully.');

        return redirect(route('qrcodes.index'));
    }

    public function show_payment_page(Request $request){
        /**
         * receive the buyer email
         * Retrive user id usung the buyer email
         * Redirect to paystack payment page
         * Initiate transaction 
         */

         $input = $request->all();

         //get the user with this email
         $user = User::where('email', $input['email'])->first();
         if(empty($user)){ 
             //user does not exit
            //create user account

            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['email']),
            ]);


         }
         
         //get the qrcode details
         
         $qrcode = QrcodeModel::where('id', $input['qrcode_id'])->first();
        //  $buyer_id = $user->id;
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'qrcode_id' => $qrcode->id,
            'status' => 'initiated',
            'qrcode_owner_id' => $qrcode->user_id,
            'payment_method' => 'paystack',
            'amount' => $qrcode->amount


        ]);

         return view('qrcodes.paystack-form',['qrcode' => $qrcode, 'transaction' => $transaction , 'user' => $user]);

    }
}
