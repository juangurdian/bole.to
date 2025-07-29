<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('post_reactions', static function (Blueprint $table) {
            $table->id();
            
            $table->string('reaction_type', 20); // like, fire, laugh, celebrate
            
            // Foreign keys
            $table->foreignId('post_id')->constrained('event_posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('attendee_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            
            // Ensure one reaction per user per post
            $table->unique(['post_id', 'user_id'], 'unique_user_post_reaction');
            
            // Indexes
            $table->index('post_id');
            $table->index('user_id');
            $table->index('attendee_id');
            $table->index('account_id');
            $table->index('reaction_type');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_reactions');
    }
}; 