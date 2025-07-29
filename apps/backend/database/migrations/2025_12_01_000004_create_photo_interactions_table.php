<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('photo_interactions', static function (Blueprint $table) {
            $table->id();
            
            $table->string('type', 20); // reaction, comment
            $table->text('content')->nullable(); // For comments
            $table->string('reaction_type', 20)->nullable(); // For reactions: like, fire, laugh, celebrate
            
            // Foreign keys
            $table->foreignId('photo_id')->constrained('event_photos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            
            // Indexes
            $table->index('photo_id');
            $table->index('user_id');
            $table->index('account_id');
            $table->index('type');
            $table->index('reaction_type');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photo_interactions');
    }
}; 