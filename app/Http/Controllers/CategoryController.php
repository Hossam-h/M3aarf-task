<?php

namespace App\Http\Controllers;

use App\Http\Requests\FetchCategoriesRequest;
use App\Repositories\Interfaces\PlaylistRepositoryInterface;
use App\Services\AIService;
use App\Services\YouTubeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * CategoryController
 *
 * Handles the main pages of the application:
 * - Index page with category input form
 * - Fetch action to process categories via AI + YouTube
 * - Results page with filtered, paginated playlist cards
 */
class CategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param AIService $aiService
     * @param YouTubeService $youtubeService
     * @param PlaylistRepositoryInterface $playlistRepository
     */
    public function __construct(
        private AIService $aiService,
        private YouTubeService $youtubeService,
        private PlaylistRepositoryInterface $playlistRepository
    ) {}

    /**
     * Display the main page with the categories input form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('index');
    }

    /**
     * Handle the form submission to fetch playlists for given categories.
     *
     * Workflow:
     * 1. Validate input via FetchCategoriesRequest
     * 2. Split textarea into individual categories
     * 3. For each category: generate AI search queries → search YouTube
     * 4. Redirect with success message
     *
     * @param FetchCategoriesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fetch(FetchCategoriesRequest $request)
    {
        $categories    = $request->getCategories();
        $totalSaved    = 0;
        $totalSearched = 0;

        try {
            foreach ($categories as $category) {
                Log::info("Processing category: {$category}");

                // Step 1: Generate course title queries via AI
                $titles = $this->aiService->generateCourseTitles($category);

                // Step 2: Search YouTube for each generated title
                foreach ($titles as $title) {
                    $this->youtubeService->searchPlaylists($title, $category);
                    $totalSearched++;
                }
            }

            $message = "Successfully processed " . count($categories) . " categories with {$totalSearched} search queries.";
            Log::info($message);

            return redirect()->route('results')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('CategoryController: Error during fetch process.', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while fetching playlists. Please check the logs.')
                ->withInput();
        }
    }

    /**
     * Display the results page with paginated and filterable playlist cards.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function results(Request $request)
    {
        $category       = $request->query('category');
        $playlists      = $this->playlistRepository->getAllPaginated(12, $category);
        $categoryCounts = $this->playlistRepository->getCategoryCounts();
        $totalCount     = $this->playlistRepository->getTotalCount();

        return view('results', compact(
            'playlists',
            'categoryCounts',
            'totalCount',
            'category'
        ));
    }
}
