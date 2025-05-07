<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('device_type', ['desktop', 'mobile', 'tablet', 'browser_extension', 'other'])->default('other');
            $table->timestamp('last_active')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->text('auth_token');
            $table->string('ip_address');
            $table->text('user_agent');
            $table->boolean('is_trusted')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
