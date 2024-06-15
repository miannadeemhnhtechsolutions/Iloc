<?php

namespace App\Http\Controllers;

use App\Models\NewPaymentMethod;
use App\Models\NewPlan;
use App\Models\NewSubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Cashier\Cashier;
use Stripe\Subscription;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\PaymentIntent;


class TestController extends Controller
{
    public function subscribe(Request $request)
    {
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
        $user_id=Auth::user()->id;
        $findPlan=NewPlan::where('id',$request->plan_id)->first();
        $price=$findPlan->price;

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Create a charge
            $existing_customer = Customer::all(['email' => Auth::user()->email])->data;

            if (count($existing_customer) > 0) {
                // Use the existing customer's ID
                $customer_id = $existing_customer[0]->id;
            } else {

                $customer = Customer::create([
                    'email' => Auth::user()->email,

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
    public function showLoginForm()
    {

        if (Auth::check()) {
            return redirect()->intended('/dashboard'); // Redirect if already logged in
        }

        return view('login');
    }
    public function login(Request $request)
    {

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            return redirect()->intended('/view');
        }

        return redirect()->route('login')->with('error', 'Invalid username or password');
    }
}
