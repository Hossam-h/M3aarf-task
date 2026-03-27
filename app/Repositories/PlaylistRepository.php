<?php

namespace App\Repositories;

use App\Models\Playlist;
use App\Repositories\Interfaces\PlaylistRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * PlaylistRepository
 *
 * Concrete implementation of PlaylistRepositoryInterface.
 * Handles all Eloquent-based data access for playlists.
 */
class PlaylistRepository implements PlaylistRepositoryInterface
{
    /**
     * Create a new repository instance.
     *
     * @param Playlist $model
     */
    public function __construct(
        protected Playlist $model
    ) {}

    /**
     * Find a playlist by its YouTube playlist ID.
     *
     * @param string $playlistId
     * @return Playlist|null
     */
    public function findByPlaylistId(string $playlistId): ?Playlist
    {
        return $this->model->where('playlist_id', $playlistId)->first();
    }

    /**
     * Create a new playlist record.
     *
     * @param array $data
     * @return Playlist
     */
    public function create(array $data): Playlist
    {
        return $this->model->create($data);
    }

    /**
     * Get all playlists with optional category filter, paginated.
     *
     * @param int $perPage
     * @param string|null $category
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 12, ?string $category = null): LengthAwarePaginator
    {
        return $this->model
            ->byCategory($category)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends(['category' => $category]);
    }

    /**
     * Get all distinct categories.
     *
     * @return Collection
     */
    public function getCategories(): Collection
    {
        return $this->model
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }

    /**
     * Get category names with their playlist counts.
     *
     * @return Collection
     */
    public function getCategoryCounts(): Collection
    {
        return $this->model
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('category')
            ->get();
    }

    /**
     * Get total playlist count.
     *
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->model->count();
    }
}
