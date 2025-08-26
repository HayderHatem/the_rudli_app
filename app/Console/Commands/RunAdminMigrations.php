<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunAdminMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:migrate {--seed : Also run the admin seeders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run admin migrations on the admin database connection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Running admin migrations...');

        // Run admin migrations
        Artisan::call('migrate', [
            '--database' => 'admin',
            '--path' => 'database/migrations/admin',
            '--force' => true,
        ]);

        $this->info(Artisan::output());

        if ($this->option('seed')) {
            $this->info('Running admin seeders...');
            Artisan::call('db:seed', [
                '--database' => 'admin',
                '--class' => 'AdminSeeder',
            ]);
            $this->info(Artisan::output());
        }

        $this->info('Admin migrations completed successfully!');

        return Command::SUCCESS;
    }
}
