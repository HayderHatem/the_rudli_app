<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Admin\Client;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $tenant = $this->resolveTenant($request);

        if ($tenant) {
            $this->setTenantConnection($tenant);
            app()->instance('tenant', $tenant);
        }

        return $next($request);
    }

    /**
     * Resolve the tenant from the request
     */
    protected function resolveTenant(Request $request)
    {
        // For now, we'll use a simple subdomain or parameter-based tenant resolution
        // You can extend this to use domain-based or other methods

        $tenantIdentifier = null;

        // Try to get tenant from subdomain
        $host = $request->getHost();
        $parts = explode('.', $host);

        if (count($parts) > 2) {
            $tenantIdentifier = $parts[0];
        }

        // Fallback: get tenant from request parameter
        if (!$tenantIdentifier) {
            $tenantIdentifier = $request->get('tenant', 'default');
        }

        // Skip tenant resolution for admin routes
        if ($request->is('admin/*')) {
            return null;
        }

        return $this->findTenant($tenantIdentifier);
    }

    /**
     * Find tenant by identifier
     */
    protected function findTenant($identifier)
    {
        // Use the admin connection to find the tenant
        $adminConnection = config('database.connections.admin');

        try {
            return DB::connection('admin')
                ->table('clients')
                ->where('identifier', $identifier)
                ->first();
        } catch (\Exception $e) {
            // If tenant table doesn't exist or connection fails, return null
            return null;
        }
    }

    /**
     * Set the database connection for the tenant
     */
    protected function setTenantConnection($tenant)
    {
        $connectionName = 'tenant_' . $tenant->identifier;

        // Create a new database connection for this tenant
        Config::set("database.connections.{$connectionName}", [
            'driver' => config('database.connections.pgsql.driver'),
            'host' => config('database.connections.pgsql.host'),
            'port' => config('database.connections.pgsql.port'),
            'database' => config('database.connections.pgsql.database'),
            'username' => config('database.connections.pgsql.username'),
            'password' => config('database.connections.pgsql.password'),
            'charset' => config('database.connections.pgsql.charset'),
            'prefix' => config('database.connections.pgsql.prefix'),
            'prefix_indexes' => config('database.connections.pgsql.prefix_indexes'),
            'search_path' => $tenant->schema_name ?: $tenant->identifier,
            'sslmode' => config('database.connections.pgsql.sslmode'),
        ]);

        // Set this as the default connection for models
        Config::set('database.default', $connectionName);

        // Purge the connection so it gets recreated with new config
        DB::purge($connectionName);
    }
}
