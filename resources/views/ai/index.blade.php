@extends('layouts.app')
@section('title', 'AI Content Studio')

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Main Generator Card -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-robot mr-2"></i>AI Content Generator</h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $remaining ?? 'Unlimited' }} generations left today</span>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('ai.generate') }}" method="POST" id="aiGenerateForm">
                    @csrf
                    <div class="form-group">
                        <label for="prompt">Content Topic / Prompt</label>
                        <textarea class="form-control @error('prompt') is-invalid @enderror" id="prompt" name="prompt" rows="3" placeholder="Describe what content you want to create..." required>{{ old('prompt') }}</textarea>
                        @error('prompt')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="content_type">Content Type</label>
                                <select class="form-control" id="content_type" name="content_type">
                                    <option value="post">Social Post</option>
                                    <option value="caption">Caption</option>
                                    <option value="hashtag">Hashtags</option>
                                    <option value="headline">Headline</option>
                                    <option value="email">Email Copy</option>
                                    <option value="ad_copy">Ad Copy</option>
                                    <option value="landing_page">Landing Page</option>
                                    <option value="blog">Blog Outline</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tone">Tone</label>
                                <select class="form-control" id="tone" name="tone">
                                    <option value="professional">Professional</option>
                                    <option value="casual">Casual</option>
                                    <option value="friendly">Friendly</option>
                                    <option value="persuasive">Persuasive</option>
                                    <option value="informative">Informative</option>
                                    <option value="humorous">Humorous</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="length">Length</label>
                                <select class="form-control" id="length" name="length">
                                    <option value="short">Short</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="long">Long</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="context">Additional Context (optional)</label>
                        <textarea class="form-control" id="context" name="context" rows="2" placeholder="Brand voice, target audience, specific details...">{{ old('context') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" id="generateBtn">
                        <i class="fas fa-magic mr-1"></i> Generate Content
                    </button>
                </form>

                <!-- Loading Indicator (hidden by default) -->
                <div id="loadingIndicator" class="text-center mt-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Generating...</span>
                    </div>
                    <p class="mt-2 text-muted">Generating your content... This may take a few seconds.</p>
                </div>

                <!-- Error Display (hidden by default) -->
                <div id="errorDisplay" class="alert alert-danger mt-4" style="display: none;">
                    <strong>Generation Failed:</strong> <span id="errorMessage"></span>
                </div>
            </div>
        </div>

        <!-- Generated Content Card (shown after generation) -->
        @if(isset($generatedContent))
        <div class="card card-success card-outline mt-4">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-check-circle mr-2"></i>Generated Content</h3>
                <div class="card-tools">
                    <button class="btn btn-sm btn-outline-light" onclick="copyToClipboard()">
                        <i class="fas fa-copy mr-1"></i> Copy
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="p-3 bg-light rounded" id="generatedText">
                    {{ $generatedContent }}
                </div>
                <hr>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" onclick="copyToClipboard()">
                        <i class="fas fa-copy mr-1"></i> Copy
                    </button>
                    <button class="btn btn-success" onclick="useAsPost()">
                        <i class="fas fa-pen mr-1"></i> Use as Post
                    </button>
                    <button class="btn btn-info" onclick="useAsContent()">
                        <i class="fas fa-save mr-1"></i> Save to Library
                    </button>
                    <a href="{{ route('ai.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo mr-1"></i> Generate More
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <!-- AI Tips Card -->
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-lightbulb mr-2"></i>AI Tips</h3>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Be specific about your target audience</li>
                    <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Include brand voice and tone preferences</li>
                    <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Mention key points you want covered</li>
                    <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Specify the platform for optimized content</li>
                    <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Add context about your industry</li>
                </ul>
            </div>
        </div>

        <!-- Recent Generations Card -->
        <div class="card card-outline card-info mt-3">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history mr-2"></i>Recent Generations</h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($recentGenerations ?? [] as $gen)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span class="badge badge-{{ $gen->content_type_color }}">{{ $gen->content_type_label }}</span>
                            <small>{{ $gen->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mt-1 mb-0 text-truncate">{{ Str::limit($gen->response_text, 60) }}</p>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-3">No generations yet</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- AI Tools Card -->
        <div class="card card-outline card-primary mt-3">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tools mr-2"></i>AI Tools</h3>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-outline-primary mb-2" onclick="showRewriteModal()"><i class="fas fa-sync mr-1"></i> Rewrite</button>
                    <button class="btn btn-outline-primary mb-2" onclick="showHashtagsModal()"><i class="fas fa-hashtag mr-1"></i> Hashtags</button>
                    <button class="btn btn-outline-primary mb-2" onclick="showIdeasModal()"><i class="fas fa-lightbulb mr-1"></i> Ideas</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rewrite Modal -->
<div class="modal fade" id="rewriteModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Rewrite Content</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('ai.rewrite') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Original Content</label>
                        <textarea name="content" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Instructions (optional)</label>
                        <input type="text" name="instructions" class="form-control" placeholder="Make it more professional...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Rewrite</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hashtags Modal -->
<div class="modal fade" id="hashtagsModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Generate Hashtags</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('ai.hashtags') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Topic</label>
                        <input type="text" name="topic" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Count</label>
                                <input type="number" name="count" class="form-control" value="10" min="1" max="30">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Platform</label>
                                <select name="platform" class="form-control">
                                    <option value="">Any</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="twitter">Twitter/X</option>
                                    <option value="linkedin">LinkedIn</option>
                                    <option value="tiktok">TikTok</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Ideas Modal -->
<div class="modal fade" id="ideasModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Content Ideas</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('ai.ideas') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Topic / Niche</label>
                        <input type="text" name="topic" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Number of Ideas</label>
                        <input type="number" name="count" class="form-control" value="5" min="1" max="10">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('aiGenerateForm');
    const loading = document.getElementById('loadingIndicator');
    const errorDiv = document.getElementById('errorDisplay');
    const errorSpan = document.getElementById('errorMessage');
    const submitBtn = document.getElementById('generateBtn');

    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Hide previous error
            errorDiv.style.display = 'none';
            
            // Show loading
            loading.style.display = 'block';
            submitBtn.disabled = true;
            
            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Reload page with generated content
                    window.location.href = window.location.pathname + '?generated=1';
                } else {
                    errorSpan.textContent = data.message || 'An error occurred during generation.';
                    errorDiv.style.display = 'block';
                }
            } catch (err) {
                errorSpan.textContent = 'Network error. Please try again.';
                errorDiv.style.display = 'block';
            } finally {
                loading.style.display = 'none';
                submitBtn.disabled = false;
            }
        });
    }
});

function copyToClipboard() {
    const text = document.getElementById('generatedText').innerText;
    navigator.clipboard.writeText(text).then(() => {
        toastr.success('Content copied to clipboard!');
    });
}

function useAsPost() {
    const text = document.getElementById('generatedText').innerText;
    sessionStorage.setItem('postContent', text);
    window.location.href = '{{ route("social.posts.create") }}';
}

function useAsContent() {
    const text = document.getElementById('generatedText').innerText;
    sessionStorage.setItem('contentBody', text);
    window.location.href = '{{ route("content.create") }}';
}

function showRewriteModal() { $('#rewriteModal').modal('show'); }
function showHashtagsModal() { $('#hashtagsModal').modal('show'); }
function showIdeasModal() { $('#ideasModal').modal('show'); }
</script>
@endpush
