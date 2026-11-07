<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\FamilyMember;
use App\Models\FeedPost;
use App\Models\PostLike;
use App\Models\PostMedia;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Log;

class FeedPostController extends Controller
{
    public function feed()
    {
        $user = Auth::user();
        $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');

        $posts = FeedPost::query()
            ->where(function ($q) use ($user, $familyIds) {
                if ($familyIds->isNotEmpty()) {
                    $q->whereIn('family_id', $familyIds)
                        ->orWhere('user_id', $user->id);
                } else {
                    $q->where('user_id', $user->id);
                }
            })
            ->with([
                'user:id,name,image',
                'sharedPost.user:id,name,image',
                'media:id,post_id,file_type,file_path',
                'likes:id,post_id,user_id',
                'comments' => function ($query) {
                    $query->whereNull('parent_id')
                        ->with([
                            'user:id,name,image',
                            'replies.user:id,name,image',
                        ])
                        ->orderBy('id', 'asc');
                },
            ])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->get()
            ->map(fn(FeedPost $post) => $this->formatPost($post));

        $stats = [
            'posts' => FeedPost::where('user_id', $user->id)->count(),
            'photos' => PostMedia::whereHas('post', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('file_type', 'image')->count(),
            'reactions' => PostLike::whereHas('post', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
            'comments' => Comment::where('user_id', $user->id)
                ->whereNull('parent_id')
                ->count(),
            'replies' => Comment::where('user_id', $user->id)
                ->whereNotNull('parent_id')
                ->count(),
        ];

        return ApiResponse::success('Feed retrieved successfully.', [
            'posts' => $posts,
            'stats' => $stats,
        ]);
    }

    public function memories()
    {
        $user = Auth::user();

        $memories = FeedPost::query()
            ->where('user_id', $user->id)
            ->with([
                'user:id,name,image',
                'sharedPost.user:id,name,image',
                'media:id,post_id,file_type,file_path',
                'likes:id,post_id,user_id',
                'comments' => function ($query) {
                    $query->whereNull('parent_id')
                        ->with([
                            'user:id,name,image',
                            'replies.user:id,name,image',
                        ])
                        ->orderBy('id', 'asc');
                },
            ])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->get()
            ->map(fn(FeedPost $post) => $this->formatPost($post));

        return ApiResponse::success('Memories retrieved successfully.', [
            'memories' => $memories,
        ]);
    }

    public function voiceMemories()
    {
        $user = Auth::user();

        $voiceMemories = FeedPost::query()
            ->where('user_id', $user->id)
            ->whereHas('media', function ($query) {
                $query->where('file_type', 'audio');
            })
            ->with([
                'media' => function ($query) {
                    $query->where('file_type', 'audio')->orderBy('id');
                },
            ])
            ->latest('created_at')
            ->get()
            ->map(function (FeedPost $post) {
                return [
                    'id' => $post->id,
                    'message' => $post->content,
                    'audio_urls' => $post->media
                        ->map(function (PostMedia $media) {
                            return filter_var($media->file_path, FILTER_VALIDATE_URL)
                                ? $media->file_path
                                : asset($media->file_path);
                        })
                        ->values(),
                    'type' => $post->type,
                    'created_at' => $post->created_at,
                ];
            })
            ->values();

        return ApiResponse::success('Voice memories retrieved successfully.', [
            'voice_memories' => $voiceMemories,
        ]);
    }

    public function storeFeedPost(Request $request)
    {
        $validated = $request->validate([
            'content' => 'nullable|string|max:5000',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
            'audio' => 'nullable|file|mimes:mp3,wav,m4a,ogg,webm|max:10240',
            'recorded_audio' => 'nullable|string',
        ]);

        $hasRecordedAudio = $request->filled('recorded_audio');

        if (
            ! $request->filled('content')
            && ! $request->hasFile('images')
            && ! $request->hasFile('audio')
            && ! $hasRecordedAudio
        ) {
            return ApiResponse::error(
                'Please add text, image, or audio to create a post.',
                ['content' => ['Please add text, image, or audio to create a post.']],
                422
            );
        }

        try {
            $post = DB::transaction(function () use ($request, $hasRecordedAudio, $validated) {
                $hasImages = $request->hasFile('images');
                $hasAudio = $request->hasFile('audio');
                $hasText = $request->filled('content');

                $type = 'text';
                if (($hasImages || $hasAudio) && $hasText) {
                    $type = 'mixed';
                } elseif ($hasImages && ! $hasAudio) {
                    $type = 'image';
                } elseif ($hasAudio && ! $hasImages) {
                    $type = 'voice';
                } elseif ($hasAudio && $hasImages) {
                    $type = 'mixed';
                } elseif ($hasRecordedAudio && $hasImages) {
                    $type = 'mixed';
                } elseif ($hasRecordedAudio) {
                    $type = 'voice';
                }

                $user = Auth::user();
                $member = FamilyMember::where('user_id', $user->id)->select('family_id')->first();

                if (! $member || ! $member->family_id) {
                    throw new \RuntimeException('Family profile is missing.');
                }

                $post = FeedPost::create([
                    'user_id' => $user->id,
                    'content' => $validated['content'] ?? null,
                    'family_id' => $member->family_id,
                    'type' => $type,
                ]);

                if ($hasImages) {
                    foreach ($request->file('images') as $image) {
                        $path = uploadGeneralImage($image, 'feed/images');
                        PostMedia::create([
                            'post_id' => $post->id,
                            'file_type' => 'image',
                            'file_path' => $path,
                        ]);
                    }
                }

                if ($hasAudio) {
                    $audio = $request->file('audio');
                    $audioDir = public_path('uploads/feed/audio');

                    if (! File::isDirectory($audioDir)) {
                        File::makeDirectory($audioDir, 0777, true, true);
                    }

                    $audioName = formatFileName($audio);
                    $audio->move($audioDir, $audioName);

                    PostMedia::create([
                        'post_id' => $post->id,
                        'file_type' => 'audio',
                        'file_path' => 'uploads/feed/audio/' . $audioName,
                    ]);
                }

                if (! $hasAudio && $hasRecordedAudio) {
                    $recordedAudio = $request->input('recorded_audio');
                    $audioMime = 'audio/webm';
                    $audioData = $recordedAudio;

                    if (preg_match('/^data:(audio\/[a-zA-Z0-9.+-]+);base64,(.*)$/s', $recordedAudio, $matches)) {
                        $audioMime = $matches[1];
                        $audioData = $matches[2];
                    }

                    $binaryAudio = base64_decode($audioData, true);

                    if ($binaryAudio === false) {
                        throw new \RuntimeException('Invalid recorded audio payload.');
                    }

                    $extensionMap = [
                        'audio/webm' => 'webm',
                        'audio/ogg' => 'ogg',
                        'audio/mpeg' => 'mp3',
                        'audio/mp3' => 'mp3',
                        'audio/mp4' => 'm4a',
                        'audio/x-m4a' => 'm4a',
                        'audio/wav' => 'wav',
                        'audio/wave' => 'wav',
                    ];

                    $audioExtension = $extensionMap[$audioMime] ?? 'webm';
                    $audioDir = public_path('uploads/feed/audio');

                    if (! File::isDirectory($audioDir)) {
                        File::makeDirectory($audioDir, 0777, true, true);
                    }

                    $audioName = 'voice-memory-' . time() . '_' . uniqid() . '.' . $audioExtension;
                    File::put($audioDir . DIRECTORY_SEPARATOR . $audioName, $binaryAudio);

                    PostMedia::create([
                        'post_id' => $post->id,
                        'file_type' => 'audio',
                        'file_path' => 'uploads/feed/audio/' . $audioName,
                    ]);
                }

                return $post->load([
                    'user:id,name,image',
                    'media:id,post_id,file_type,file_path',
                    'likes:id,post_id,user_id',
                    'comments' => function ($query) {
                        $query->whereNull('parent_id')
                            ->with([
                                'user:id,name,image',
                                'replies.user:id,name,image',
                            ])
                            ->orderBy('id', 'asc');
                    },
                ])->loadCount(['likes', 'comments']);
            });

            return ApiResponse::success('Post created successfully.', [
                'post' => $this->formatPost($post),
            ]);
        } catch (\Throwable $th) {
            \Log::error('storeFeedPost failed: ' . $th->getMessage());

            return ApiResponse::error('Unable to create post right now. Please try again.', null, 500);
        }
    }

    public function toggleFeedLike(FeedPost $post)
    {
        try {
            if ($post->user_id === Auth::id()) {
                return ApiResponse::error("You can't react to your own post.", null, 422);
            }

            $like = PostLike::where('post_id', $post->id)
                ->where('user_id', Auth::id())
                ->first();

            if ($like) {
                $like->delete();
                $liked = false;
                $message = "You've unliked this post.";
            } else {
                PostLike::create([
                    'post_id' => $post->id,
                    'user_id' => Auth::id(),
                ]);

                $liked = true;
                $message = "You've liked this post!";
            }

            // Refresh post data
            $post->load([
                'user',
                'media'
                // Add any other relationships you need
            ]);

            // Total likes
            $totalLikes = PostLike::where('post_id', $post->id)->count();

            return ApiResponse::success($message, [
                'liked' => $liked,
                'total_likes' => $totalLikes,
                'post' => $post,
            ]);
        } catch (\Throwable $th) {

            dd($th);
            \Log::error('toggleFeedLike failed: ' . $th->getMessage(), [
                'post_id' => $post->id,
                'user_id' => Auth::id(),
            ]);

            return ApiResponse::error(
                'Unable to update reaction right now. Please try again.',
                null,
                500
            );
        }
    }

    public function shareFeedPost(FeedPost $post)
    {
        try {
            $user = Auth::user();
            $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');

            if (! $this->canAccessFeedPost($post, $user->id, $familyIds)) {
                return ApiResponse::error('You are not allowed to share this post.', null, 403);
            }

            $member = FamilyMember::where('user_id', $user->id)->select('family_id')->first();

            if (! $member || ! $member->family_id) {
                throw new \RuntimeException('Family profile is missing.');
            }

            $sharedPost = DB::transaction(function () use ($post, $user, $member) {
                $post->loadMissing('media');

                $sharedPost = FeedPost::create([
                    'user_id' => $user->id,
                    'shared_post_id' => $post->id,
                    'content' => $post->content,
                    'family_id' => $member->family_id,
                    'type' => $post->type ?? 'text',
                ]);

                foreach ($post->media as $media) {
                    $sharedPath = $this->duplicateFeedMediaPath($media->file_path);

                    PostMedia::create([
                        'post_id' => $sharedPost->id,
                        'file_type' => $media->file_type,
                        'file_path' => $sharedPath,
                    ]);
                }

                return $sharedPost->load([
                    'user:id,name,image',
                    'sharedPost.user:id,name,image',
                    'media:id,post_id,file_type,file_path',
                    'likes:id,post_id,user_id',
                    'comments' => function ($query) {
                        $query->whereNull('parent_id')
                            ->with([
                                'user:id,name,image',
                                'replies.user:id,name,image',
                            ])
                            ->orderBy('id', 'asc');
                    },
                ])->loadCount(['likes', 'comments']);
            });

            return ApiResponse::success('Post shared successfully.', [
                'post' => $this->formatPost($sharedPost),
            ]);
        } catch (\Throwable $th) {
            \Log::error('shareFeedPost failed: ' . $th->getMessage(), [
                'post_id' => $post->id ?? null,
                'user_id' => Auth::id(),
            ]);

            return ApiResponse::error('Unable to share the post right now. Please try again.', null, 500);
        }
    }

    // public function toggleFeedLike(FeedPost $post)
    // {
    //     // dd($post);
    //     try {
    //         if ($post->user_id === Auth::id()) {
    //             return ApiResponse::error("You can't react to your own post.", null, 422);
    //         }

    //         $like = PostLike::where('post_id', $post->id)
    //             ->where('user_id', Auth::id())
    //             ->first();

    //         if ($like) {
    //             $like->delete();

    //             return ApiResponse::success("You've unliked this post.", [
    //                 'liked' => false,
    //             ]);
    //         }

    //         PostLike::create([
    //             'post_id' => $post->id,
    //             'user_id' => Auth::id(),
    //         ]);

    //         return ApiResponse::success("You've liked this post!", [
    //             'liked' => true,
    //         ]);
    //     } catch (\Throwable $th) {
    //         Log::error('toggleFeedLike failed: ' . $th->getMessage(), [
    //             'post_id' => $post->id,
    //             'user_id' => Auth::id(),
    //         ]);

    //         return ApiResponse::error('Unable to update reaction right now. Please try again.', null, 500);
    //     }
    // }

    public function storeFeedComment(Request $request, FeedPost $post)
    {
        $request->validate([
            'comment' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        try {
            $userId = Auth::id();

            if ($post->user_id === $userId) {
                return ApiResponse::error("You can't comment on your own post.", null, 422);
            }

            if ($request->parent_id) {
                $parentComment = Comment::where('id', $request->parent_id)
                    ->where('post_id', $post->id)
                    ->first();

                if (! $parentComment) {
                    return ApiResponse::error('Invalid reply comment.', null, 422);
                }
            }

            $comment = Comment::create([
                'post_id' => $post->id,
                'user_id' => $userId,
                'comment' => $request->input('comment'),
                'parent_id' => $request->parent_id ?? null,
            ])->load('user:id,name,image');

            return ApiResponse::success('Comment added.', [
                'comment' => $this->formatComment($comment),
            ]);
        } catch (\Throwable $th) {
            return ApiResponse::error('Unable to add comment right now.', null, 500);
        }
    }

    public function storeFeedReply(Request $request, FeedPost $post, $commentId)
    {
        $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        $comment = Comment::find($commentId);

        if (!$comment) {
            return ApiResponse::error('The comment you are trying to reply to was not found.', null, 404);
        }

        if ((int) $comment->post_id !== (int) $post->id) {
            return ApiResponse::error('Invalid reply target.', null, 422);
        }

        try {
            $reply = Comment::create([
                'post_id'   => $post->id,
                'user_id'   => Auth::id(),
                'comment'   => $request->input('comment'),
                'parent_id' => $comment->id,
            ])->load('user:id,name,image');

            return ApiResponse::success('Reply added successfully.', [
                'reply' => $this->formatComment($reply),
            ]);
        } catch (\Throwable $th) {
            return ApiResponse::error('Unable to add reply right now. Please try again later.', null, 500);
        }
    }

    public function destroyFeedPost($postId)
    {
        $post = FeedPost::with('media')->find($postId);

        if (!$post) {
            return ApiResponse::error('The post you are trying to delete was not found.', null, 404);
        }

        if ((int) $post->user_id !== (int) Auth::id()) {
            return ApiResponse::error('You are not allowed to delete this post.', null, 403);
        }

        try {
            foreach ($post->media as $media) {
                if (
                    $media->file_path &&
                    !filter_var($media->file_path, FILTER_VALIDATE_URL) &&
                    File::exists(public_path($media->file_path))
                ) {
                    File::delete(public_path($media->file_path));
                }
            }

            $post->delete();

            return ApiResponse::success('Post deleted successfully.');
        } catch (\Throwable $th) {
            return ApiResponse::error('Unable to delete the post right now. Please try again later.', null, 500);
        }
    }

    private function canAccessFeedPost(FeedPost $post, int $userId, $familyIds): bool
    {
        if ((int) $post->user_id === $userId) {
            return true;
        }

        return $familyIds->isNotEmpty()
            && in_array((int) $post->family_id, $familyIds->map(fn ($id) => (int) $id)->all(), true);
    }

    private function duplicateFeedMediaPath(string $filePath): string
    {
        if (filter_var($filePath, FILTER_VALIDATE_URL)) {
            return $filePath;
        }

        $sourcePath = public_path($filePath);

        if (! File::exists($sourcePath)) {
            return $filePath;
        }

        $directory = dirname($sourcePath);
        $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
        $filename = pathinfo($sourcePath, PATHINFO_FILENAME);
        $copiedName = $filename . '-share-' . time() . '_' . uniqid();

        if ($extension) {
            $copiedName .= '.' . $extension;
        }

        $targetPath = $directory . DIRECTORY_SEPARATOR . $copiedName;
        File::copy($sourcePath, $targetPath);

        $relativePath = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $targetPath);

        return str_replace('\\', '/', $relativePath);
    }

    private function formatPost(FeedPost $post): array
    {
        return [
            'id' => $post->id,
            'user_id' => $post->user_id,
            'shared_post_id' => $post->shared_post_id,
            'family_id' => $post->family_id,
            'content' => $post->content,
            'type' => $post->type,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
            'user' => $post->relationLoaded('user') && $post->user ? [
                'id' => $post->user->id,
                'name' => $post->user->name,
                'image' => $post->user->image,
            ] : null,
            'shared_post' => $post->relationLoaded('sharedPost') && $post->sharedPost ? [
                'id' => $post->sharedPost->id,
                'user_id' => $post->sharedPost->user_id,
                'content' => $post->sharedPost->content,
                'type' => $post->sharedPost->type,
                'shared_post_id' => $post->sharedPost->shared_post_id,
                'user' => $post->sharedPost->relationLoaded('user') && $post->sharedPost->user ? [
                    'id' => $post->sharedPost->user->id,
                    'name' => $post->sharedPost->user->name,
                    'image' => $post->sharedPost->user->image,
                ] : null,
            ] : null,
            'media' => $post->relationLoaded('media')
                ? $post->media->map(fn(PostMedia $media) => [
                    'id' => $media->id,
                    'post_id' => $media->post_id,
                    'file_type' => $media->file_type,
                    'file_path' => filter_var($media->file_path, FILTER_VALIDATE_URL)
                        ? $media->file_path
                        : asset($media->file_path),
                ])->values()
                : [],
            'likes_count' => $post->likes_count ?? $post->likes->count(),
            'comments_count' => $post->comments_count ?? $post->comments->count(),
            'likes' => $post->relationLoaded('likes')
                ? $post->likes->map(fn($like) => [
                    'id' => $like->id,
                    'post_id' => $like->post_id,
                    'user_id' => $like->user_id,
                ])->values()
                : [],
            'comments' => $post->relationLoaded('comments')
                ? $post->comments->map(fn($comment) => $this->formatComment($comment))->values()
                : [],
        ];
    }

    private function formatComment(Comment $comment): array
    {
        return [
            'id' => $comment->id,
            'post_id' => $comment->post_id,
            'user_id' => $comment->user_id,
            'comment' => $comment->comment,
            'parent_id' => $comment->parent_id,
            'user' => $comment->relationLoaded('user') && $comment->user ? [
                'id' => $comment->user->id,
                'name' => $comment->user->name,
                'image' => $comment->user->image,
            ] : null,
            'replies' => $comment->relationLoaded('replies')
                ? $comment->replies->map(fn($reply) => $this->formatComment($reply))->values()
                : [],
        ];
    }
}
