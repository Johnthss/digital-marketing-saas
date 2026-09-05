<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\SocialPost;
use App\Models\SocialAccount;
use App\Models\Campaign;
use App\Services\Social\SocialPostService;
use App\Services\ContentQualityScorer;
use App\Enums\PostStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocialPostController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request)
    {
        $agency = $request->user()->agency;

        $query = SocialPost::where('agency_id', $agency->id);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }
        if ($request->filled('search')) {
            $query->where('content', 'like', '%' . $request->search . '%');
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(15);

        $platforms = SocialAccount::SUPPORTED_PLATFORMS;
        $statuses = [
            'draft' => 'Draft',
            'scheduled' => 'Scheduled',
            'published' => 'Published',
            'failed' => 'Failed',
        ];

        return view('social.posts.index', compact('agency', 'posts', 'platforms', 'statuses'));
    }

    public function create(Request $request)
    {
        $agency = $request->user()->agency;
        $accounts = SocialAccount::where('agency_id', $agency->id)->active()->get();
        $campaigns = Campaign::where('agency_id', $agency->id)->active()->get();

        return view('social.posts.create', compact('agency', 'accounts', 'campaigns'));
    }

    public function store(Request $request, SocialPostService $postService)
    {
        $agency = $request->user()->agency;

        $validated = $request->validate([
            'social_account_id' => 'required|exists:social_accounts,id',
            'content' => 'required|string|max:5000',
            'media' => 'nullable|array',
            'hashtags' => 'nullable|array',
            'scheduled_at' => 'nullable|date|after:now',
            'campaign_id' => 'nullable|exists:campaigns,id',
        ]);

        $account = SocialAccount::findOrFail($validated['social_account_id']);

        if ($account->agency_id !== $agency->id) {
            abort(403);
        }

        // Quality scoring
        $scorer = app(ContentQualityScorer::class);
        $qualityScore = $scorer->score(new SocialPost([
            'content' => $validated['content'],
            'platform' => $account->platform,
            'media' => $validated['media'] ?? [],
            'hashtags' => $validated['hashtags'] ?? [],
        ]));

        $post = $postService->createPost($agency, [
            'social_account_id' => $validated['social_account_id'],
            'platform' => $account->platform,
            'content' => $validated['content'],
            'media' => $validated['media'] ?? null,
            'hashtags' => $validated['hashtags'] ?? null,
            'status' => isset($validated['scheduled_at']) ? PostStatus::SCHEDULED->value : PostStatus::DRAFT->value,
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'quality_score' => $qualityScore,
        ]);

        // Attach to campaign if specified
        if (!empty($validated['campaign_id'])) {
            $post->campaigns()->attach($validated['campaign_id']);
        }

        $agency->increment('posts_count');

        return redirect()->route('social.posts.index')
            ->with('success', 'Post created successfully.');
    }

    public function show(Request $request, SocialPost $post)
    {
        $agency = $request->user()->agency;

        if ($post->agency_id !== $agency->id) {
            abort(403);
        }

        return view('social.posts.show', compact('agency', 'post'));
    }

    public function edit(Request $request, SocialPost $post)
    {
        $agency = $request->user()->agency;

        if ($post->agency_id !== $agency->id) {
            abort(403);
        }

        $accounts = SocialAccount::where('agency_id', $agency->id)->active()->get();
        $campaigns = Campaign::where('agency_id', $agency->id)->active()->get();

        return view('social.posts.edit', compact('agency', 'post', 'accounts', 'campaigns'));
    }

    public function update(Request $request, SocialPost $post)
    {
        $agency = $request->user()->agency;

        if ($post->agency_id !== $agency->id) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:5000',
            'media' => 'nullable|array',
            'hashtags' => 'nullable|array',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $post->update([
            'content' => $validated['content'],
            'media' => $validated['media'] ?? $post->media,
            'hashtags' => $validated['hashtags'] ?? $post->hashtags,
            'scheduled_at' => $validated['scheduled_at'] ?? $post->scheduled_at,
        ]);

        return redirect()->route('social.posts.index')
            ->with('success', 'Post updated successfully.');
    }

    public function destroy(Request $request, SocialPost $post)
    {
        $agency = $request->user()->agency;

        if ($post->agency_id !== $agency->id) {
            abort(403);
        }

        $post->delete();
        $agency->decrement('posts_count');

        return redirect()->route('social.posts.index')
            ->with('success', 'Post deleted.');
    }

    public function publish(Request $request, SocialPost $post, SocialPostService $postService)
    {
        $agency = $request->user()->agency;

        if ($post->agency_id !== $agency->id) {
            abort(403);
        }

        $result = $postService->publishPost($post);

        if ($result['success']) {
            return redirect()->route('social.posts.index')->with('success', 'Post published successfully!');
        }

        return back()->with('error', 'Failed to publish: ' . $result['message']);
    }

    public function retry(Request $request, SocialPost $post, SocialPostService $postService)
    {
        $agency = $request->user()->agency;

        if ($post->agency_id !== $agency->id) {
            abort(403);
        }

        $result = $postService->retryPost($post);

        if ($result['success']) {
            return redirect()->route('social.posts.index')->with('success', 'Post retried successfully!');
        }

        return back()->with('error', $result['message']);
    }

    public function score(Request $request, SocialPost $post, ContentQualityScorer $scorer)
    {
        $agency = $request->user()->agency;

        if ($post->agency_id !== $agency->id) {
            abort(403);
        }

        $score = $scorer->score($post);
        $label = $scorer->getLabel($score);

        $post->update(['quality_score' => $score]);

        return response()->json([
            'score' => $score,
            'label' => $label,
        ]);
    }
}
