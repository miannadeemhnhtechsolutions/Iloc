<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
//        $user = Auth::user();
//        dd(request()->user());
         $user=$request->user();
        $today_date=Carbon::now()->format('Y-m-d');
//        dd($user);
        if (!$user || !$user->package_subscription) {

            $response = [
                'status' => false,
                'message' => "Please subscribe to access",

            ];
            return response()->json($response, 404);
        }elseif ($user && $user->package_subscription->expiry_date < $today_date ){


            $response = [
                'status' => false,
                'message' => "Subscription period has been expired",

            ];
            return response()->json($response, 404);
        }elseif ($user && $user->package_subscription->expiry_date >= $today_date && $user->package_subscription->status=="paused"){
            $response = [
                'status' => false,
                'message' => "Subscription has been paused",

            ];
            return response()->json($response, 404);
        }elseif ($user && $user->package_subscription->expiry_date >= $today_date && $user->package_subscription->status=="canceled"){
            $response = [
                'status' => false,
                'message' => "Subscription has been canceled",

            ];
            return response()->json($response, 404);
        }else{
            return $next($request);
        }



    }
}
