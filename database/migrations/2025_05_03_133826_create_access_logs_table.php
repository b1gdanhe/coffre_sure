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
        Schema::create('access_logs', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->enum('action', [
                'login', 'logout', 'failed_login', 'password_change', 
                'mfa_enabled', 'mfa_disabled', 'entry_view', 'entry_create', 
                'entry_update', 'entry_delete', 'vault_create', 'vault_update', 
                'vault_delete', 'vault_share', 'export_data', 'import_data',
                'device_added', 'device_removed', 'other'
            ]);
            $table->text('details')->nullable();
            $table->string('ip_address');
            $table->text('device_info');
            $table->enum('status', ['success', 'failed', 'blocked', 'suspicious'])->default('success');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_logs');
    }
};
