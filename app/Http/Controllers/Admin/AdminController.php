<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Client;
use App\Models\Admin\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard
     */
    public function index()
    {
        $stats = [
            'total_clients' => Client::count(),
            'active_clients' => Client::where('is_active', true)->count(),
            'total_subscriptions' => Subscription::count(),
            'active_subscriptions' => Subscription::where('is_active', true)->count(),
        ];

        $recentClients = Client::with('subscription')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentClients'));
    }

    /**
     * Create a new schema for a client
     */
    public function createClientSchema(Request $request, Client $client)
    {
        $request->validate([
            'schema_name' => 'required|string|alpha_dash|unique:clients,schema_name,' . $client->id,
        ]);

        try {
            $schemaName = $request->schema_name;

            // Create the schema
            DB::connection('admin')->statement("CREATE SCHEMA IF NOT EXISTS {$schemaName}");

            // Update client with schema name
            $client->update(['schema_name' => $schemaName]);

            // Copy base tables to new schema (users, etc.)
            $this->copyBaseTablesToSchema($schemaName);

            return redirect()->back()->with('success', "Schema '{$schemaName}' created successfully for client {$client->name}");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create schema: ' . $e->getMessage());
        }
    }

    /**
     * Copy base Laravel tables to new client schema
     */
    protected function copyBaseTablesToSchema($schemaName)
    {
        $baseTables = [
            'users',
            'password_reset_tokens',
            'sessions',
            'cache',
            'jobs',
            'job_batches',
            'failed_jobs'
        ];

        foreach ($baseTables as $table) {
            try {
                // Create table structure in new schema
                DB::connection('admin')->statement("
                    CREATE TABLE IF NOT EXISTS {$schemaName}.{$table} 
                    (LIKE public.{$table} INCLUDING ALL)
                ");
            } catch (\Exception $e) {
                // Log error but continue with other tables
                \Illuminate\Support\Facades\Log::warning("Failed to copy table {$table} to schema {$schemaName}: " . $e->getMessage());
            }
        }
    }
}
