<?php

namespace App\Repositories\Interfaces;

use App\Models\Playlist;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Interface PlaylistRepositoryInterface
 *
 * Contract for playlist data access operations.
 * Provides abstraction over the persistence layer.
 */
interface PlaylistRepositoryInterface
{
    /**
     * Find a playlist by its YouTube playlist ID.
     *
     * @param string $playlistId YouTube playlist ID
     * @return Playlist|null
     */
    public function findByPlaylistId(string $playlistId): ?Playlist;

    /**
     * Create a new playlist record.
     *
     * @param array $data Playlist data
     * @return Playlist
     */
    public function create(array $data): Playlist;

    /**
     * Get all playlists with optional category filter, paginated.
     *
     * @param int $perPage Number of results per page
     * @param string|null $category Filter by category (null = all)
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 12, ?string $category = null): LengthAwarePaginator;

    /**
     * Get all distinct categories.
     *
     * @return Collection
     */
    public function getCategories(): Collection;

    /**
     * Get category names with their playlist counts.
     *
     * @return Collection
     */
    public function getCategoryCounts(): Collection;

    /**
     * Get total playlist count.
     *
     * @return int
     */
    public function getTotalCount(): int;
}
