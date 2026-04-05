<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();

            $table->enum('status', [
                'trialing',
                'active',
                'past_due',
                'canceled'
            ])->default('trialing');

            $table->string('currency', 3);

            $table->decimal('amount', 10, 2);

            $table->timestamp('started_at')->nullable();

            $table->timestamp('trial_ends_at')->nullable();

            $table->timestamp('grace_ends_at')->nullable();

            $table->timestamp('ends_at')->nullable();

            $table->boolean('access_granted')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};