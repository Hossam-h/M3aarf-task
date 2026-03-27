<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the playlists table to store YouTube playlist data.
     */
    public function up(): void
    {
        Schema::create('playlists', function (Blueprint $table) {
            $table->id();
            $table->string('playlist_id')->unique();    // YouTube playlist ID (deduplicated)
            $table->string('title');                      // Playlist title
            $table->text('description')->nullable();      // Playlist description
            $table->string('thumbnail');                  // Thumbnail URL
            $table->string('channel_name');               // YouTube channel name
            $table->string('category');                   // Category this playlist belongs to
            $table->integer('video_count')->default(0);   // Number of videos in playlist
            $table->timestamps();

            // Index for faster category filtering
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playlists');
    }
};
