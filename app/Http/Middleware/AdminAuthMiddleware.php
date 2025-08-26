<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class AdminAuthMiddleware
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
        // Ensure we're using the admin (public schema) connection
        $this->setAdminConnection();

        // For now, we'll implement a simple authentication check
        // You can extend this to use proper admin authentication

        // Check if admin is authenticated
        if (!$this->isAdminAuthenticated($request)) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }

    /**
     * Set the admin database connection (public schema)
     */
    protected function setAdminConnection()
    {
        // Ensure admin connection uses public schema
        Config::set('database.connections.admin', [
            'driver' => config('database.connections.pgsql.driver'),
            'host' => config('database.connections.pgsql.host'),
            'port' => config('database.connections.pgsql.port'),
            'database' => config('database.connections.pgsql.database'),
            'username' => config('database.connections.pgsql.username'),
            'password' => config('database.connections.pgsql.password'),
            'charset' => config('database.connections.pgsql.charset'),
            'prefix' => config('database.connections.pgsql.prefix'),
            'prefix_indexes' => config('database.connections.pgsql.prefix_indexes'),
            'search_path' => 'public',
            'sslmode' => config('database.connections.pgsql.sslmode'),
        ]);

        // Set admin as default for admin routes
        Config::set('database.default', 'admin');
    }

    /**
     * Check if admin is authenticated
     */
    protected function isAdminAuthenticated(Request $request)
    {
        // For now, return true to allow development
        // You should implement proper admin authentication here
        return true;

        // Example implementation:
        // return Auth::guard('admin')->check();
    }
}
