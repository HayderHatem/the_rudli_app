<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\Subscription;
use App\Models\Admin\Client;
use App\Models\Admin\AdminUser;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default subscriptions
        $basicPlan = Subscription::create([
            'name' => 'Basic Plan',
            'price' => 19.99,
            'billing_cycle' => 'monthly',
            'features' => ['Basic Support', 'Up to 10 Users', '1GB Storage'],
            'max_users' => 10,
            'max_storage' => 1073741824, // 1GB
            'is_active' => true,
        ]);

        $proPlan = Subscription::create([
            'name' => 'Pro Plan',
            'price' => 49.99,
            'billing_cycle' => 'monthly',
            'features' => ['Priority Support', 'Up to 50 Users', '10GB Storage', 'Advanced Features'],
            'max_users' => 50,
            'max_storage' => 10737418240, // 10GB
            'is_active' => true,
        ]);

        $enterprisePlan = Subscription::create([
            'name' => 'Enterprise Plan',
            'price' => 199.99,
            'billing_cycle' => 'monthly',
            'features' => ['24/7 Support', 'Unlimited Users', '100GB Storage', 'All Features', 'Custom Integrations'],
            'max_users' => 1000,
            'max_storage' => 107374182400, // 100GB
            'is_active' => true,
        ]);

        // Create a demo client
        $demoClient = Client::create([
            'name' => 'Demo Company',
            'identifier' => 'demo',
            'schema_name' => 'demo',
            'domain' => 'demo.localhost',
            'subscription_id' => $basicPlan->id,
            'is_active' => true,
            'settings' => [
                'theme' => 'default',
                'timezone' => 'UTC',
            ],
        ]);

        // Create super admin user
        AdminUser::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'client_id' => null,
            'is_active' => true,
        ]);

        // Create client admin for demo company
        AdminUser::create([
            'name' => 'Demo Admin',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
            'role' => 'client_admin',
            'client_id' => $demoClient->id,
            'is_active' => true,
        ]);

        $this->command->info('Admin seeding completed!');
        $this->command->info('Super Admin: admin@example.com / password');
        $this->command->info('Demo Client Admin: demo@example.com / password');
        $this->command->info('Demo Client Identifier: demo');
    }
}
