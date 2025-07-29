<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_posts', static function (Blueprint $table) {
            $table->id();
            
            $table->text('content');
            $table->string('type', 50)->default('text'); // text, image, poll, announcement
            $table->text('media_url')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_organizer_post')->default(false);
            $table->bigInteger('reply_to_id')->nullable();
            
            // Foreign keys
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('attendee_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            
            // Self-referential foreign key for replies
            $table->foreign('reply_to_id')->references('id')->on('event_posts')->onDelete('cascade');
            
            // Indexes
            $table->index('event_id');
            $table->index('user_id');
            $table->index('attendee_id');
            $table->index('account_id');
            $table->index('created_at');
            $table->index('is_pinned');
            $table->index('type');
            $table->index('reply_to_id');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_posts');
    }
}; 