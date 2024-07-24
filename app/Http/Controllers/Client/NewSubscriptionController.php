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
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class NewSubscriptionController extends Controller
{

    private $apiContext;

    public function __construct()
    {
        // Set up PayPal API context
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(env('PAYPAL_CLIENT_ID'), env('PAYPAL_CLIENT_SECRET'))
        );
        $this->apiContext->setConfig([
            'mode' => 'live', // Change from 'sandbox' to 'live' for production
        ]);
    }
//    abc



    public function all_plans(){
        $plans=NewPlan::all();
        $response = [
            'status' => true,
            'data'=>$plans,
            'message' => "Success",
        ];
        return response()->json($response, 200);
    }
    public function subscribe_package(Request $request){
//        dd($request->all());
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|numeric|exists:new_plans,id',
            "payment_method"=>"required|string",
            "email"=>"required|email",
            "name"=>"required",
            "city"=>"required",
            'state'=>"required",
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
        $uniqueTransactionId = uniqid('txn_', true);
        $today_date = Carbon::now()->format('Y-m-d');
        $today_date=Carbon::now()->format('Y-m-d');
        $email=$request->email;
        $name=$request->name;
        $city=$request->city;
        $state=$request->state;
//        $address=$request->address;
        $address=$request->address;
        $today_date=Carbon::now()->format('Y-m-d');
        $checkSubscriptions=NewSubscriptionPlan::where('email',$email)->first();
        if ($checkSubscriptions){
           if ($checkSubscriptions->expiry_date > $today_date){
               $response = [
                   'status' => false,
                   'message' => "You have already active subscription",

               ];
               return response()->json($response, 404);
           }
        }



        $findPlan=NewPlan::where('id',$request->plan_id)->first();
        $price=$findPlan->price;

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $existing_customer = Customer::all(['email' => $email])->data;

            if (count($existing_customer) > 0) {
                // Use the existing customer's ID
                $customer_id = $existing_customer[0]->id;
            } else {

                $customer = Customer::create([
                    'email' => $email,

                ]);
                $customer_id = $customer->id;
            }
            $paymentIntent = PaymentIntent::create([
                'amount' => $price* 100,
                'currency' => 'usd',
                'payment_method' => $request->payment_method,

                'description' => $findPlan->description,
                'confirmation_method' => 'manual',
                'confirm' => true,
                'return_url' => 'https://iloc.dev-mn.xyz/',
                'customer' => $customer_id,
            ]);
            $find_plan=NewPlan::where('id',$request->plan_id)->first();
            if ($find_plan->interval=="month"){
                $newDateTime = Carbon::now()->addDays(30);

            }else{
                $newDateTime = Carbon::now()->addYear(1);

            }
            $subscription=NewSubscriptionPlan::where(['email'=>$email,'new_plan_id'=>$request->plan_id])->first();
            $start_date= Carbon::now()->format('Y-m-d');

            if ($subscription){
                $subscription->update(['start_date'=>$start_date,'expiry_date'=>$newDateTime,'new_plan_id'=>$request->plan_id,'status'=>"active",
                    'email'=>$email,'name'=>$name, 'city'=>$city,'state'=>$state,'address'=>$address,'transaction_id'=>$uniqueTransactionId]);
            }else{
                $subscription= NewSubscriptionPlan::create(['start_date'=>$start_date,'expiry_date'=>$newDateTime,'new_plan_id'=>$request->plan_id,'status'=>"active",
                    'email'=>$email,'name'=>$name, 'city'=>$city,'state'=>$state,'address'=>$address,'transaction_id'=>$uniqueTransactionId]);
            }
            $secret=env('STRIPE_SECRET');
            $public=env('STRIPE_KEY');
            $payment_record= NewPaymentMethod::create(['name'=>'Stripe','slug'=>'stripe','public_key'=>$public,'secret_key'=>$secret,
                'is_active'=>1,'subscription_id'=>$subscription->id,'transaction_id'=>$uniqueTransactionId, 'email'=>$email]);
            return response()->json(['message' => 'subscription successful']);
        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function handlePayment(Request $request)
    {

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
        } $uniqueTransactionId = uniqid('txn_', true);
//        dd(1);
//        dd(Auth::user());
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $amount = new Amount();
        $findPlan=NewPlan::where('id',$request->plan_id)->first();
        $price=$findPlan->price;
        $price=$price.".00";
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
        $address = rawurlencode($address);
        $name = rawurlencode($name);
        $city = rawurlencode($city);
        $state = rawurlencode($state);
        $email = rawurlencode($email);


        $redirectUrls = new \PayPal\Api\RedirectUrls();
//        $redirectUrls->setReturnUrl(url('payment/success/'.$request->plan_id.'/'.$user_id.'/'))
//            ->setCancelUrl(url('payment/cancel/'.$request->plan_id.'/'.$user_id.'/'));
        $redirectUrls->setReturnUrl('https://iloc.dev-bt.xyz/success/'.$request->plan_id.'/'.$uniqueTransactionId .'/'.$email.'/'.$name.'/'.$city.'/'.$state.'/'.$address.'/')
            ->setCancelUrl('https://iloc.dev-bt.xyz/error/'.$request->plan_id.'/'.$uniqueTransactionId .'/'.$email.'/'.$name.'/'.$city.'/'.$state.'/'.$address.'/');
//dd(1);
        $payment = new Payment();
        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions([$transaction])
            ->setRedirectUrls($redirectUrls);
//dd(2);
//        dd(1);
        \Log::info('Payment Data:', [
            'intent' => 'sale',
            'payer' => $payer->toArray(),
            'transactions' => [$transaction->toArray()],
            'redirectUrls' => $redirectUrls->toArray(),
        ]);
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
//            return redirect()->away($createdPayment->getApprovalLink());
            $response = [
                'status' => true,
                'link'    => $createdPayment->getApprovalLink(),
                'message' => "Success",
            ];


            return response()->json($response, 200);


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
//            return $request->all();

            $plan_id=$request->input('planId');
            $email=$request->input('email');
            $name=$request->input('name');
            $city=$request->input('city');
            $state=$request->input('state');
            $address=$request->input('address');
            $uniqueTransactionId=$request->input('txnId');



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
                        'email'=>$email,'transaction_id'=>$uniqueTransactionId,]);
                }
                $secret=env('PAYPAL_CLIENT_SECRET');
                $public=env('PAYPAL_CLIENT_ID');

                $payment_record= NewPaymentMethod::create(['name'=>'PayPal','slug'=>'paypal','public_key'=>$public,'secret_key'=>$secret,
                    'is_active'=>1,'subscription_id'=>$subscription->id,'paymentId'=>$request->input('paymentId'),'PayerID'=>$request->input('PayerID'),'email'=>$email,'transaction_id'=>$uniqueTransactionId]);

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
