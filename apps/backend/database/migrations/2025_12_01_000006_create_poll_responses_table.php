<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('poll_responses', static function (Blueprint $table) {
            $table->id();
            
            $table->jsonb('selected_options'); // Array of option IDs or values
            
            // Foreign keys
            $table->foreignId('poll_id')->constrained('event_polls')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('attendee_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            
            // Ensure one response per user per poll
            $table->unique(['poll_id', 'user_id'], 'unique_user_poll_response');
            
            // Indexes
            $table->index('poll_id');
            $table->index('user_id');
            $table->index('attendee_id');
            $table->index('account_id');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poll_responses');
    }
}; 