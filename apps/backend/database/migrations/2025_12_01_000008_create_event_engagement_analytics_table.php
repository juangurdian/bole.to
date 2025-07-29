<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_engagement_analytics', static function (Blueprint $table) {
            $table->id();
            
            $table->date('date');
            $table->integer('total_posts')->default(0);
            $table->integer('total_reactions')->default(0);
            $table->integer('total_photos')->default(0);
            $table->integer('unique_engaged_users')->default(0);
            $table->decimal('poll_participation_rate', 5, 2)->nullable();
            
            // Foreign keys
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            
            // Ensure unique analytics per event per date
            $table->unique(['event_id', 'date'], 'unique_event_date_analytics');
            
            // Indexes
            $table->index('event_id');
            $table->index('account_id');
            $table->index('date');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_engagement_analytics');
    }
}; 