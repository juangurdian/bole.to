<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_polls', static function (Blueprint $table) {
            $table->id();
            
            $table->text('question');
            $table->string('type', 20)->default('multiple_choice'); // multiple_choice, rating, open_ended
            $table->jsonb('options')->nullable(); // Array of {id, text, color} for choices
            $table->boolean('allows_multiple')->default(false);
            $table->boolean('is_anonymous')->default(true);
            $table->timestamp('closes_at')->nullable();
            
            // Foreign keys
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            
            // Indexes
            $table->index('event_id');
            $table->index('created_by');
            $table->index('account_id');
            $table->index('type');
            $table->index('closes_at');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_polls');
    }
}; 