<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SocialPostRequest;
use App\Http\Resources\SocialPostResource;
use App\Models\SocialPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiSocialPostController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'agency']);
    }

    public function index(Request $request): JsonResponse
    {
        $agency = $request->user()->agency;
        $query = SocialPost::where('agency_id', $agency->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('platform')) {
            $query->where('platform', $request->platform);
        }

        $posts = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return SocialPostResource::collection($posts)->response();
    }

    public function store(SocialPostRequest $request): JsonResponse
    {
        $agency = $request->user()->agency;
        $validated = $request->validated();
        $validated['platform'] ??= 'twitter';
        $validated['social_account_id'] ??= null;
        $post = SocialPost::create([
            'agency_id' => $agency->id,
            ...$validated,
        ]);

        return (new SocialPostResource($post))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, SocialPost $post): JsonResponse
    {
        $this->authorizeAccess($request, $post);

        return (new SocialPostResource($post->load('campaigns', 'socialAccount')))->response();
    }

    public function update(SocialPostRequest $request, SocialPost $post): JsonResponse
    {
        $this->authorizeAccess($request, $post);
        $post->update($request->validated());

        return (new SocialPostResource($post))->response();
    }

    public function destroy(Request $request, SocialPost $post): JsonResponse
    {
        $this->authorizeAccess($request, $post);
        $post->delete();

        return response()->json(null, 204);
    }

    private function authorizeAccess(Request $request, SocialPost $post): void
    {
        $agency = $request->user()->agency;
        if ($post->agency_id !== $agency->id) {
            abort(404);
        }
    }
}
