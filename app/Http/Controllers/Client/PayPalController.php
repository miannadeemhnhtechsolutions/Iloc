<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\NewPaymentMethod;
use App\Models\NewPlan;
use App\Models\NewSubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;

class PayPalController extends Controller
{
    private $apiContext;

    public function __construct()
    {
        // Set up PayPal API context
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(env('PAYPAL_CLIENT_ID'), env('PAYPAL_CLIENT_SECRET'))
        );
        $this->apiContext->setConfig([
            'mode' => 'sandbox', // Change to 'live' for production
        ]);
    }

    public function index(){
        return view('paypal');
    }


    public function handlePayment(Request $request)
    {
//        dd(Auth::user());

        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|numeric|exists:new_plans,id',
            "email"=>"required|email",
            "name"=>"required",
            "city"=>"required",
            "state"=>"required",
            "address"=>"required",
        ]);
        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);


        }

        $today_date=Carbon::now()->format('Y-m-d');
        $checkSubscriptions=NewSubscriptionPlan::where('email',$request->email)->first();
        if ($checkSubscriptions){
            if ($checkSubscriptions->expiry_date > $today_date){
                $response = [
                    'status' => false,
                    'message' => "You have already active subscription",

                ];
                return response()->json($response, 404);
            }
        }
//        $user_id=Auth::user()->id;
        $uniqueTransactionId = uniqid('txn_', true);
        $today_date = Carbon::now()->format('Y-m-d');
//        dd(1);
//        dd(Auth::user());
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $amount = new Amount();
        $findPlan=NewPlan::where('id',$request->plan_id)->first();
        $price=$findPlan->price;
        $price = $findPlan->price . ".00";
//        dd($price);
//        dd($request->amount);
        $amount->setTotal($price);
        $amount->setCurrency('USD');

        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $email=$request->email;
        $name=$request->name;
        $city=$request->city;
        $state=$request->state;
        $address=$request->address;


        $redirectUrls = new \PayPal\Api\RedirectUrls();

//        $redirectUrls->setReturnUrl('/payment/success/'.$request->plan_id.'/'.$uniqueTransactionId .'/'.$email.'/'.$name.'/'.$city.'/'.$state.'/'.$address.'/')
//            ->setCancelUrl('/payment/cancel/'.$request->plan_id.'/'.$uniqueTransactionId .'/'.$email.'/'.$name.'/'.$city.'/'.$state.'/'.$address.'/');
        $redirectUrls->setReturnUrl(url('/payment/success/'.$request->plan_id.'/'.$uniqueTransactionId .'/'.$email.'/'.$name.'/'.$city.'/'.$state.'/'.$address.'/'))
            ->setCancelUrl(url('/payment/cancel/'.$request->plan_id.'/'.$uniqueTransactionId .'/'.$email.'/'.$name.'/'.$city.'/'.$state.'/'.$address.'/'));

//dd(1);
//dd(1);
        $payment = new Payment();
        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions([$transaction])
            ->setRedirectUrls($redirectUrls);
//dd(2);
        try {

            $createdPayment = $payment->create($this->apiContext);

            // Log the response for debugging
            \Log::info('PayPal API Response: ' . json_encode($createdPayment));

            // Check if the response is a string (indicating an error message)
            if (is_string($createdPayment)) {
                // Handle the error message
                return response()->json(['error' => $createdPayment], 500);
            }

            // If the response is not a string, assume it's the expected object
            return redirect()->away($createdPayment->getApprovalLink());
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            // Handle PayPal connection exceptions
            return response()->json(['error' => $ex->getMessage()], 500);
        } catch (\Exception $ex) {
            // Handle other exceptions
            return response()->json(['error' => $ex->getMessage()], 500);
        }

    }

    public function paymentSuccess(Request $request,$plan_id,$uniqueTransactionId,$email,$name,$city,$state,$address)
    {
        // Check if payment is successful
        if ($request->input('paymentId') && $request->input('PayerID')) {
            $paymentId = $request->input('paymentId');
            $payerId = $request->input('PayerID');

            $payment = Payment::get($paymentId, $this->apiContext);

            $execution = new \PayPal\Api\PaymentExecution();
            $execution->setPayerId($payerId);

            try {
                $result = $payment->execute($execution, $this->apiContext);
                $newDateTime =  now()->format('Y-m-d');
                $find_plan=NewPlan::where('id',$plan_id)->first();
                if ($find_plan->interval=="month"){
                    $newDateTime = Carbon::now()->addDays(30);

                }else{
                    $newDateTime = Carbon::now()->addYear(1);

                }
                $subscription=NewSubscriptionPlan::where(['email'=>$email,'new_plan_id'=>$plan_id])->first();
                $start_date= Carbon::now()->format('Y-m-d');

                if ($subscription){
                    $subscription->update(['start_date'=>$start_date,'expiry_date'=>$newDateTime,'new_plan_id'=>$plan_id,'name'=>$name,
                        'city'=>$city,'state'=>$state,'address'=>$address,'status'=>"active",
                        'email'=>$email,'transaction_id'=>$uniqueTransactionId]);
                }else{
                    $subscription= NewSubscriptionPlan::create(['start_date'=>$start_date,'expiry_date'=>$newDateTime,'new_plan_id'=>$plan_id,'status'=>"active",
                        'name'=>$name, 'city'=>$city,'state'=>$state,'address'=>$address,
                        'email'=>$email,'transaction_id'=>$uniqueTransactionId]);
                }
//                $start_date = Carbon::now()->format('Y-m-d');
//                $subscription = NewSubscriptionPlan::create([
//                    'start_date' => $start_date,
//                    'expiry_date' => $newDateTime,
//                    'new_plan_id' => $plan_id,
//                    'status' => "active",
////                    'user_id'=>1,
//                    'transaction_id' => $uniqueTransactionId,
//                    'email'=>$email,
//                ]);
                $secret=env('PAYPAL_CLIENT_SECRET');
                $public=env('PAYPAL_CLIENT_ID');

                $payment_record= NewPaymentMethod::create(['name'=>'PayPal','slug'=>'paypal','public_key'=>$public,'secret_key'=>$secret,
                    'is_active'=>1,'subscription_id'=>$subscription->id,'paymentId'=>$request->input('paymentId'),'PayerID'=>$request->input('PayerID'),
                        'transaction_id' => $uniqueTransactionId,'email'=>$email]);

                $response = [
                    'status' => true,
                    'data'=>$subscription,
                    'message' => "Subscription  successful",
                ];
                return response()->json($response, 200);
            } catch (\Exception $ex) {


                return response()->json(['error' => $ex->getMessage()], 500);
            }
        } else {
            return response()->json(['error' => 'Payment was not successful.'], 500);
        }
    }

    public function paymentCancel(Request $request,$plan_id,$uniqueTransactionId,$email,$name,$city,$state,$address)

    {





        return response()->json(['message' => 'Payment was canceled.']);
    }
}
