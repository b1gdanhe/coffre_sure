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
        Schema::create('entries', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->foreignUuid('vault_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('username')->nullable();
            $table->text('password')->nullable();
            $table->text('url')->nullable();
            $table->text('notes')->nullable();
            $table->string('icon')->nullable();
            $table->timestamp('last_used')->nullable();
            $table->enum('category', [
                'login', 'card', 'identity', 'secure_note', 
                'crypto', 'medical', 'wifi', 'document', 'other'
            ])->default('login');
            $table->boolean('favorite')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
