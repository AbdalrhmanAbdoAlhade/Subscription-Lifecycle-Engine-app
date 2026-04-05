<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function pay(Request $request, $subscriptionId)
    {
        $subscription = Subscription::findOrFail($subscriptionId);

        $payment = Payment::create([
            'subscription_id' => $subscription->id,
            'amount' => $subscription->amount,
            'currency' => $subscription->currency,
            'status' => 'success',
            'paid_at' => now(),
        ]);

        $subscription->update([
            'status' => 'active',
            'grace_ends_at' => null,
            'access_granted' => true,
        ]);

        return response()->json([
            'message' => 'Payment successful',
            'payment' => $payment,
            'subscription' => $subscription
        ]);
    }

    public function fail($subscriptionId)
    {
        $subscription = Subscription::findOrFail($subscriptionId);

        $payment = Payment::create([
            'subscription_id' => $subscription->id,
            'amount' => $subscription->amount,
            'currency' => $subscription->currency,
            'status' => 'failed',
            'paid_at' => now(),
            'failure_reason' => 'Payment failed',
        ]);

        $subscription->update([
            'status' => 'past_due',
            'grace_ends_at' => now()->addDays(3),
            'access_granted' => true,
        ]);

        return response()->json([
            'message' => 'Payment failed, grace period started',
            'payment' => $payment,
            'subscription' => $subscription
        ]);
    }
}