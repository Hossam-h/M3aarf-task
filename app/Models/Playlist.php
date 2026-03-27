<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Playlist Model
 *
 * Represents a YouTube playlist stored in the database.
 * Uses a unique constraint on playlist_id for deduplication.
 */
class Playlist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'playlist_id',
        'title',
        'description',
        'thumbnail',
        'channel_name',
        'category',
        'video_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'video_count' => 'integer',
    ];

    /**
     * Scope: Filter playlists by category.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, ?string $category)
    {
        if ($category && $category !== 'all') {
            return $query->where('category', $category);
        }

        return $query;
    }
}
