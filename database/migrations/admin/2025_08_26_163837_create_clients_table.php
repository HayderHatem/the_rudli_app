<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The database connection that should be used by the migration.
     */
    protected $connection = 'admin';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('admin')->create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('identifier')->unique();
            $table->string('schema_name')->nullable();
            $table->string('domain')->nullable();
            $table->foreignId('subscription_id')->constrained('subscriptions');
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('admin')->dropIfExists('clients');
    }
};
