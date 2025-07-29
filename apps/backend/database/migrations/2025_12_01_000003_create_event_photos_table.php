<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_photos', static function (Blueprint $table) {
            $table->id();
            
            $table->text('photo_url');
            $table->text('thumbnail_url')->nullable();
            $table->text('blur_hash')->nullable(); // For placeholder while loading
            $table->timestamp('taken_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('reveal_at');
            $table->boolean('is_revealed')->default(false);
            $table->integer('view_count')->default(0);
            
            // Foreign keys
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('attendee_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            
            // Indexes
            $table->index('event_id');
            $table->index('attendee_id');
            $table->index('account_id');
            $table->index(['event_id', 'reveal_at'], 'idx_photos_event_reveal');
            $table->index('is_revealed');
            $table->index('taken_at');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_photos');
    }
}; 