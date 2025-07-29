<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_push_tokens', static function (Blueprint $table) {
            $table->id();
            
            $table->text('token');
            $table->string('platform', 20); // ios, android, web
            $table->text('device_id')->nullable();
            $table->boolean('is_active')->default(true);
            
            // Foreign keys
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            
            // Ensure unique token per user (allow same token for different users)
            $table->unique(['user_id', 'token'], 'unique_user_token');
            
            // Indexes
            $table->index('user_id');
            $table->index('account_id');
            $table->index('platform');
            $table->index('is_active');
            $table->index('device_id');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_push_tokens');
    }
}; 