# Multi-Panel Laravel Application

A Laravel application with dual panel architecture featuring admin and client panels with multi-tenant database schema separation.

## Architecture Overview

This application implements a multi-panel structure with:

1. **Admin Panel** - Super admin interface for managing clients and subscriptions
2. **App Panel** - Client interface (default panel) for client-specific operations
3. **Multi-tenant Database** - Separate schemas for each client with shared admin tables

## Features

### Admin Panel (`/admin`)
- Manage clients and their database schemas
- Create and manage subscription plans
- Super admin and client admin user management
- All admin data stored in `public` schema

### App Panel (default `/`)
- Client-specific dashboard and operations
- Tenant-aware routing and database connections
- Each client operates in their own database schema
- Automatic tenant resolution from subdomain or parameters

### Multi-Tenancy
- **Admin tables**: Stored in `public` schema (PostgreSQL)
- **Client tables**: Each client has dedicated schema
- **Tenant resolution**: Via subdomain, domain, or parameters
- **Schema isolation**: Complete data separation between clients

## Database Structure

### Admin Schema (`public`)
- `admin_users` - Super admins and client admins
- `clients` - Client organizations and configurations
- `subscriptions` - Subscription plans and pricing
- Standard Laravel tables (migrations, etc.)

### Client Schemas (per tenant)
- `users` - Client-specific users
- `[client_tables]` - Client-specific application data
- Copies of Laravel framework tables

## Setup Instructions

### 1. Environment Configuration

```bash
# Database (PostgreSQL recommended for schema support)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Run Migrations

```bash
# Run standard Laravel migrations (these will go to admin/public schema)
php artisan migrate --database=admin

# Run admin-specific migrations with seed data
php artisan admin:migrate --seed
```

**Important**: All Laravel base migrations now use the `admin` connection and create tables in the `public` schema. When new client schemas are created, these tables are copied over automatically.

### 4. Build Assets

```bash
npm run build
# or for development
npm run dev
```

## Usage

### Admin Panel Access
- URL: `http://your-domain/admin`
- Default credentials: `admin@example.com` / `password`

### Client Panel Access
- URL: `http://your-domain` (default panel)
- URL: `http://demo.your-domain` (tenant: demo)
- URL: `http://your-domain?tenant=demo` (parameter-based)

### Creating New Clients

1. Access admin panel
2. Create subscription plan
3. Create client with unique identifier
4. System automatically creates client schema
5. Client can access via their identifier

### Tenant Resolution

The application resolves tenants via:
1. **Subdomain**: `clientname.domain.com`
2. **Parameter**: `domain.com?tenant=clientname`
3. **Fallback**: Default tenant or demo mode

## Key Components

### Middleware
- `TenantMiddleware`: Resolves tenant and sets database connection
- `AdminAuthMiddleware`: Ensures admin routes use public schema

### Models
- `App\Models\Admin\Client`: Client management
- `App\Models\Admin\Subscription`: Subscription plans
- `App\Models\Admin\AdminUser`: Admin user management

### Controllers
- `Admin\AdminController`: Admin dashboard and client management
- `Admin\UserController`: Admin user management
- `Admin\SubscriptionController`: Subscription management
- `App\DashboardController`: Client dashboard
- `App\UserController`: Client user management

### Commands
- `php artisan admin:migrate`: Run admin migrations
- `php artisan admin:migrate --seed`: Run with sample data

## Development Notes

### Adding New Admin Features
1. Create models in `App\Models\Admin\` namespace
2. Use `'admin'` database connection
3. Create migrations in `database/migrations/admin/`
4. Add routes to admin prefix group

### Adding New Client Features
1. Create standard Laravel models
2. Models automatically use tenant connection
3. Create standard migrations
4. Add routes to app group or default routes

### Database Connections
- **admin**: Always uses public schema
- **tenant_[identifier]**: Dynamically created per tenant
- **default**: Points to current tenant or admin based on context

## Security Considerations

1. **Schema Isolation**: Complete data separation between tenants
2. **Admin Authentication**: Separate authentication for admin panels
3. **Connection Management**: Automatic connection switching prevents data leaks
4. **Input Validation**: All tenant identifiers are validated and sanitized

## Deployment

1. Set up PostgreSQL database
2. Configure environment variables
3. Run migrations: `php artisan migrate --database=admin && php artisan admin:migrate --seed`
4. Configure web server for subdomain routing (if using subdomain tenancy)
5. Set up SSL certificates for all domains/subdomains

## Customization

### Adding Tenant Resolution Methods
Modify `TenantMiddleware::resolveTenant()` to add custom tenant resolution logic.

### Custom Admin Authentication
Update `AdminAuthMiddleware` to implement your preferred admin authentication system.

### Schema Customization
Modify `AdminController::copyBaseTablesToSchema()` to customize which tables are copied to new tenant schemas.

## Troubleshooting

### Common Issues
1. **Connection errors**: Verify database configuration and PostgreSQL setup
2. **Schema not found**: Ensure client schema was created via admin panel
3. **Tenant not resolved**: Check subdomain configuration or parameter format
4. **Migration errors**: Run admin migrations separately from standard migrations

### Debug Mode
Enable Laravel debugging and check logs for tenant resolution and database connection details.
