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
        ]);
        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);


        }
        $user_id=$request->user()->id;
        $findPlan=NewPlan::where('id',$request->plan_id)->first();
        $price=$findPlan->price;

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $existing_customer = Customer::all(['email' => $request->user()->email])->data;

            if (count($existing_customer) > 0) {
                // Use the existing customer's ID
                $customer_id = $existing_customer[0]->id;
            } else {

                $customer = Customer::create([
                    'email' => $request->user()->email,

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
                'return_url' => 'https://petavengers.dev-mn.xyz/',
                'customer' => $customer_id,
            ]);
            $find_plan=NewPlan::where('id',$request->plan_id)->first();
            if ($find_plan->interval=="month"){
                $newDateTime = Carbon::now()->addDays(30);
            }else{
                $newDateTime = Carbon::now()->addYear(1);
            }
            $subscription=NewSubscriptionPlan::where(['user_id'=>$user_id,'new_plan_id'=>$request->plan_id])->first();
            $start_date= Carbon::now()->format('Y-m-d');
            if ($subscription){
                $subscription->update(['start_date'=>$start_date,'expiry_date'=>$newDateTime,'new_plan_id'=>$request->plan_id,'status'=>"active",
                    'user_id'=>$user_id]);
            }else{
                $subscription= NewSubscriptionPlan::create(['start_date'=>$start_date,'expiry_date'=>$newDateTime,'new_plan_id'=>$request->plan_id,'status'=>"active",
                    'user_id'=>$user_id]);
            }
            $secret=env('STRIPE_SECRET');
            $public=env('STRIPE_KEY');
            $payment_record= NewPaymentMethod::create(['name'=>'Stripe','slug'=>'stripe','public_key'=>$public,'secret_key'=>$secret,
                'is_active'=>1,'subscription_id'=>$subscription->id]);
            return response()->json(['message' => 'subscription successful']);
        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function handlePayment(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|numeric|exists:new_plans,id',
        ]);
        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);


        }
        $user_id=$request->user()->id;
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


        $redirectUrls = new \PayPal\Api\RedirectUrls();
//        $redirectUrls->setReturnUrl(url('payment/success/'.$request->plan_id.'/'.$user_id.'/'))
//            ->setCancelUrl(url('payment/cancel/'.$request->plan_id.'/'.$user_id.'/'));
        $redirectUrls->setReturnUrl('https://Pet-avengers.dev-bt.xyz/success/'.$request->plan_id.'/'.$user_id.'/')
            ->setCancelUrl('https://Pet-avengers.dev-bt.xyz/error/'.$request->plan_id.'/'.$user_id.'/');
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

    public function paymentSuccess(Request $request,$plan_id,$userID)
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
                $subscription=NewSubscriptionPlan::where(['user_id'=>$userID,'new_plan_id'=>$plan_id])->first();
                $start_date= Carbon::now()->format('Y-m-d');

                if ($subscription){
                    $subscription->update(['start_date'=>$start_date,'expiry_date'=>$newDateTime,'new_plan_id'=>$plan_id,'status'=>"active",
                        'user_id'=>$userID]);
                }else{
                    $subscription= NewSubscriptionPlan::create(['start_date'=>$start_date,'expiry_date'=>$newDateTime,'new_plan_id'=>$plan_id,'status'=>"active",
                        'user_id'=>$userID]);
                }
                $secret=env('PAYPAL_CLIENT_SECRET');
                $public=env('PAYPAL_CLIENT_ID');

                $payment_record= NewPaymentMethod::create(['name'=>'PayPal','slug'=>'paypal','public_key'=>$public,'secret_key'=>$secret,
                    'is_active'=>1,'subscription_id'=>$subscription->id,'paymentId'=>$request->input('paymentId'),'PayerID'=>$request->input('PayerID')]);

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

    public function paymentCancel($plan_id,$userID)

    {





        return response()->json(['message' => 'Payment was canceled.']);
    }
}
