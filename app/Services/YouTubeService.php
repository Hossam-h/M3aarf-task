<?php

namespace App\Services;

use App\Repositories\Interfaces\PlaylistRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * YouTubeService
 *
 * Handles communication with the YouTube Data API v3
 * to search for playlists and store them in the database.
 */
class YouTubeService
{
    /**
     * YouTube Data API v3 search endpoint.
     */
    private const SEARCH_URL = 'https://www.googleapis.com/youtube/v3/search';

    /**
     * YouTube Data API v3 playlists endpoint (for video count).
     */
    private const PLAYLISTS_URL = 'https://www.googleapis.com/youtube/v3/playlists';

    /**
     * The YouTube API key.
     */
    private string $apiKey;

    /**
     * Create a new YouTubeService instance.
     *
     * @param PlaylistRepositoryInterface $playlistRepository
     */
    public function __construct(
        private PlaylistRepositoryInterface $playlistRepository
    ) {
        $this->apiKey = config('services.youtube.api_key', '');
    }

    /**
     * Search YouTube for playlists matching the given query and store results.
     *
     * Calls the YouTube Data API v3 search endpoint with type=playlist.
     * For each result, checks if the playlist_id already exists in DB
     * to prevent duplicates. New playlists are inserted into the database.
     *
     * @param string $query The search query string
     * @param string $category The category this search belongs to
     * @return void
     */
    public function searchPlaylists(string $query, string $category): void
    {
        try {
            // Validate API key is configured
            if (empty($this->apiKey)) {
                Log::error('YouTubeService: YouTube API key is not configured.');
                return;
            }

            // Call YouTube Data API v3 search endpoint
            $response = Http::timeout(30)->get(self::SEARCH_URL, [
                'part'       => 'snippet',
                'type'       => 'playlist',
                'q'          => $query,
                'maxResults' => 2,
                'key'        => $this->apiKey,
            ]);

            // Check for successful response
            if ($response->failed()) {
                Log::error('YouTubeService: YouTube API search request failed.', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'query'  => $query,
                ]);
                return;
            }

            $data  = $response->json();
            $items = $data['items'] ?? [];

            if (empty($items)) {
                Log::info("YouTubeService: No playlists found for query: {$query}");
                return;
            }

            // Collect playlist IDs to fetch video counts in batch
            $playlistIds = [];
            foreach ($items as $item) {
                $playlistIds[] = $item['id']['playlistId'] ?? null;
            }
            $playlistIds = array_filter($playlistIds);

            // Fetch video counts for all playlists in one request
            $videoCounts = $this->getVideoCountsBatch($playlistIds);

            // Process each search result
            foreach ($items as $item) {
                $playlistId = $item['id']['playlistId'] ?? null;

                if (!$playlistId) {
                    continue;
                }

                // Deduplication: skip if playlist already exists in DB
                $existing = $this->playlistRepository->findByPlaylistId($playlistId);
                if ($existing) {
                    Log::info("YouTubeService: Playlist {$playlistId} already exists, skipping.");
                    continue;
                }

                $snippet = $item['snippet'] ?? [];

                // Create new playlist record
                $this->playlistRepository->create([
                    'playlist_id'  => $playlistId,
                    'title'        => $snippet['title'] ?? 'Untitled',
                    'description'  => $snippet['description'] ?? null,
                    'thumbnail'    => $snippet['thumbnails']['high']['url']
                                      ?? $snippet['thumbnails']['medium']['url']
                                      ?? $snippet['thumbnails']['default']['url']
                                      ?? '',
                    'channel_name' => $snippet['channelTitle'] ?? 'Unknown',
                    'category'     => $category,
                    'video_count'  => $videoCounts[$playlistId] ?? 0,
                ]);

                Log::info("YouTubeService: Saved playlist: {$snippet['title']} ({$playlistId})");
            }

        } catch (\Exception $e) {
            Log::error('YouTubeService: Exception occurred while searching playlists.', [
                'query'    => $query,
                'category' => $category,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    /**
     * Fetch video counts for multiple playlists in a single API call.
     *
     * @param array $playlistIds Array of YouTube playlist IDs
     * @return array Associative array [playlistId => videoCount]
     */
    private function getVideoCountsBatch(array $playlistIds): array
    {
        $videoCounts = [];

        if (empty($playlistIds)) {
            return $videoCounts;
        }

        try {
            $response = Http::timeout(15)->get(self::PLAYLISTS_URL, [
                'part' => 'contentDetails',
                'id'   => implode(',', $playlistIds),
                'key'  => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                foreach ($data['items'] ?? [] as $item) {
                    $id    = $item['id'];
                    $count = $item['contentDetails']['itemCount'] ?? 0;
                    $videoCounts[$id] = (int) $count;
                }
            }
        } catch (\Exception $e) {
            Log::warning('YouTubeService: Failed to fetch video counts.', [
                'error' => $e->getMessage(),
            ]);
        }

        return $videoCounts;
    }
}
