<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscriptions
     */
    public function index()
    {
        $subscriptions = Subscription::withCount('clients')->paginate(15);
        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show the form for creating a new subscription
     */
    public function create()
    {
        return view('admin.subscriptions.create');
    }

    /**
     * Store a newly created subscription
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'features' => 'nullable|array',
            'max_users' => 'required|integer|min:1',
            'max_storage' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        Subscription::create([
            'name' => $request->name,
            'price' => $request->price,
            'billing_cycle' => $request->billing_cycle,
            'features' => $request->features ?? [],
            'max_users' => $request->max_users,
            'max_storage' => $request->max_storage,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription created successfully.');
    }

    /**
     * Display the specified subscription
     */
    public function show(Subscription $subscription)
    {
        $subscription->load('clients');
        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Show the form for editing the specified subscription
     */
    public function edit(Subscription $subscription)
    {
        return view('admin.subscriptions.edit', compact('subscription'));
    }

    /**
     * Update the specified subscription
     */
    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'features' => 'nullable|array',
            'max_users' => 'required|integer|min:1',
            'max_storage' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $subscription->update([
            'name' => $request->name,
            'price' => $request->price,
            'billing_cycle' => $request->billing_cycle,
            'features' => $request->features ?? [],
            'max_users' => $request->max_users,
            'max_storage' => $request->max_storage,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription updated successfully.');
    }

    /**
     * Remove the specified subscription
     */
    public function destroy(Subscription $subscription)
    {
        if ($subscription->clients()->count() > 0) {
            return redirect()->route('admin.subscriptions.index')
                ->with('error', 'Cannot delete subscription with existing clients.');
        }

        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription deleted successfully.');
    }
}
