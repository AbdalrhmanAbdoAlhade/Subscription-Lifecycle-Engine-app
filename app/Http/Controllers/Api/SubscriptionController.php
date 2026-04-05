<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlanPrice;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Subscription::with(['plan', 'payments'])
                ->where('user_id', $request->user()->id)
                ->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'plan_price_id' => 'required|exists:plan_prices,id',
        ]);

        $planPrice = PlanPrice::with('plan')->findOrFail($request->plan_price_id);

        $subscription = Subscription::create([
            'user_id' => $request->user()->id,
            'plan_id' => $planPrice->plan_id,
            'status' => $planPrice->plan->trial_days > 0 ? 'trialing' : 'active',
            'currency' => $planPrice->currency,
            'amount' => $planPrice->price,
            'started_at' => now(),
            'trial_ends_at' => $planPrice->plan->trial_days > 0
                ? now()->addDays($planPrice->plan->trial_days)
                : null,
            'access_granted' => true,
        ]);

        return response()->json($subscription, 201);
    }

    public function show($id)
    {
        return response()->json(
            Subscription::with(['plan', 'payments'])->findOrFail($id)
        );
    }

    public function cancel($id)
    {
        $subscription = Subscription::findOrFail($id);

        $subscription->update([
            'status' => 'canceled',
            'access_granted' => false,
            'ends_at' => now(),
        ]);

        return response()->json([
            'message' => 'Subscription canceled successfully by admin',
            'subscription' => $subscription
        ]);
    }
}