<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    // تم حذف الـ __construct لجعل الكود متوافقاً مع Laravel 11

    public function index()
    {
        return response()->json(Plan::with('prices')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trial_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'prices' => 'required|array',
            'prices.*.billing_cycle' => 'required|in:monthly,yearly',
            'prices.*.currency' => 'required|string|size:3',
            'prices.*.price' => 'required|numeric|min:0',
        ]);

        $plan = Plan::create($request->only([
            'name',
            'description',
            'trial_days',
            'is_active',
        ]));

        foreach ($request->prices as $price) {
            $plan->prices()->create($price);
        }

        return response()->json($plan->load('prices'), 201);
    }

    public function show($id)
    {
        return response()->json(
            Plan::with('prices')->findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);

        $plan->update($request->only([
            'name',
            'description',
            'trial_days',
            'is_active',
        ]));

        return response()->json($plan->load('prices'));
    }

    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();

        return response()->json([
            'message' => 'Plan deleted successfully'
        ]);
    }
}