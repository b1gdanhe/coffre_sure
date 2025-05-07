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
        Schema::create('shared_vaults', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->foreignUuid('vault_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->enum('permission_level', ['view', 'edit', 'manage', 'admin'])->default('view');
            $table->enum('invitation_status', ['pending', 'accepted', 'rejected', 'revoked'])->default('pending');
            $table->text('encrypted_key');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_vaults');
    }
};
