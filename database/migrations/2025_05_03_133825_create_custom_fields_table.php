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
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->foreignUuid('entry_id')->constrained()->onDelete('cascade');
            $table->string('field_name');
            $table->text('field_value');
            $table->enum('field_type', ['text', 'password', 'email', 'number', 'date', 'boolean', 'url', 'phone', 'file'])->default('text');
            $table->boolean('is_encrypted')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
