<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Event;
use App\Models\FamilyMember;
use App\Models\FeedPost;
use App\Models\PostLike;
use App\Models\PostMedia;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FeedPostController extends Controller
{
    // public function feed()
    // {
    //     $user = Auth::user();
    //     if ($user) {
    //         $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');
    //         $posts = FeedPost::query()
    //             ->where(function ($q) use ($user, $familyIds) {
    //                 if ($familyIds->isNotEmpty()) {
    //                     $q->where(function ($query) use ($familyIds) {
    //                         $query->whereIn('family_id', $familyIds)
    //                             ->where('privacy', 'public');
    //                     })->orWhere('user_id', $user->id);
    //                 } else {
    //                     $q->where('user_id', $user->id);
    //                 }
    //             })
    //             ->with([
    //                 'user:id,name,image',
    //                 'media:id,post_id,file_type,file_path',
    //                 'likes:id,post_id,user_id',
    //                 'sharedPost.user:id,name,image',
    //                 'comments' => function ($query) {
    //                     $query->whereNull('parent_id')
    //                         ->with(['user:id,name,image', 'replies.user:id,name,image']);
    //                 },
    //             ])
    //             ->withCount(['likes', 'comments'])
    //             ->latest()
    //             ->get();

    //         // Dashboard Statistics
    //         $stats = [
    //             'posts' => FeedPost::where('user_id', $user->id)->count(),
    //             'photos' => PostMedia::whereHas('post', function ($q) use ($user) {
    //                 $q->where('user_id', $user->id);
    //             })->where('file_type', 'image')->count(),
    //             'reactions' => PostLike::whereHas('post', function ($q) use ($user) {
    //                 $q->where('user_id', $user->id);
    //             })->count(),
    //             'comments' => Comment::where('user_id', $user->id)
    //                 ->whereNull('parent_id')
    //                 ->count(),
    //             'replies' => Comment::where('user_id', $user->id)
    //                 ->whereNotNull('parent_id')
    //                 ->count(),
    //         ];
    //     } else {
    //         $posts = FeedPost::where('privacy', 'public')
    //             ->with(['user:id,name,image', 'media'])
    //             ->latest()
    //             ->get();

    //         $stats = null;
    //     }

    //     return view('user.feed', compact('posts', 'stats'));
    // }

    public function feed()
    {
        $user = Auth::user();
        if ($user) {
            $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');

            $posts = FeedPost::query()
                ->where(function ($q) use ($user, $familyIds) {
                    if ($familyIds->isNotEmpty()) {
                        $q->where(function ($query) use ($familyIds) {
                            $query->whereIn('family_id', $familyIds)
                                ->where('privacy', 'public');
                        })->orWhere('user_id', $user->id);
                    } else {
                        $q->where('user_id', $user->id);
                    }
                })
                ->with([
                    'user:id,name,image',
                    'media:id,post_id,file_type,file_path',
                    'likes:id,post_id,user_id',
                    'sharedPost.user:id,name,image',
                    'comments' => function ($query) {
                        $query->whereNull('parent_id')
                            ->with(['user:id,name,image', 'replies.user:id,name,image']);
                    },
                ])
                ->withCount(['likes', 'comments'])
                ->latest()
                ->get();

            // Dashboard Statistics
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

            // --- Upcoming Events Query ---
            $upcomingEvents = Event::query()
                ->with('creator:id,name')
                ->where(function ($q) use ($user, $familyIds) {
                    if ($familyIds->isNotEmpty()) {
                        $q->whereIn('family_id', $familyIds)
                            ->orWhere('creator_user_id', $user->id);
                    } else {
                        $q->where('creator_user_id', $user->id);
                    }
                })
                ->where('event_date', '>=', Carbon::now())
                ->orderBy('event_date', 'asc')
                ->take(4)
                ->get();

        } else {
            $posts = FeedPost::where('privacy', 'public')
                ->with(['user:id,name,image', 'media'])
                ->latest()
                ->get();

            $stats = null;
            $upcomingEvents = collect(); 
        }

        return view('user.feed', compact('posts', 'stats', 'upcomingEvents'));
    }

    public function storeFeedPost(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'content' => 'nullable|string|max:5000',
            'privacy' => 'required|in:public,private',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
            'audio' => 'nullable|file|mimes:mp3,wav,m4a,ogg,webm|max:10240',
            'recorded_audio' => 'nullable|string',
        ]);

        $hasRecordedAudio = $request->filled('recorded_audio');

        if (! $request->filled('content') && ! $request->hasFile('images') && ! $request->hasFile('audio') && ! $hasRecordedAudio) {
            return back()->withErrors(['content' => 'Please add text, image, or audio to create a post.'])->withInput();
        }

        try {
            DB::transaction(function () use ($request, $hasRecordedAudio) {
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
                    'user_id' => Auth::id(),
                    'privacy' => $request->input('privacy'),
                    'content' => $request->input('content'),
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
                        'file_path' => 'uploads/feed/audio/'.$audioName,
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

                    $audioName = 'voice-memory-'.time().'_'.uniqid().'.'.$audioExtension;
                    File::put($audioDir.DIRECTORY_SEPARATOR.$audioName, $binaryAudio);

                    PostMedia::create([
                        'post_id' => $post->id,
                        'file_type' => 'audio',
                        'file_path' => 'uploads/feed/audio/'.$audioName,
                    ]);
                }
            });

            return back()->with('success', 'Post created successfully.');
        } catch (\Throwable $th) {
            \Log::error('storeFeedPost failed: '.$th->getMessage());

            return back()->with('error', 'Unable to create post right now. Please try again.');
        }
    }

    public function toggleFeedLike(FeedPost $post)
    {
        try {
            if ($post->user_id === Auth::id()) {
                return back()->with('error', "You can't react to your own post.");
            }
            $like = PostLike::where('post_id', $post->id)
                ->where('user_id', Auth::id())
                ->first();

            if ($like) {
                $like->delete();

                return back()->with('success', 'Reaction removed.');
            }

            PostLike::create([
                'post_id' => $post->id,
                'user_id' => Auth::id(),
            ]);

            return back()->with('success', 'Reacted to post.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Unable to update reaction right now.');
        }
    }

    public function storeFeedComment(Request $request, FeedPost $post)
    {
        $request->validate([
            'comment' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        try {
            $userId = Auth::id();
            // User cannot comment on their own post
            if ($post->user_id === $userId) {
                return back()->with('error', "You can't comment on your own post.");
            }

            // Validate reply belongs to same post
            if ($request->parent_id) {
                $parentComment = Comment::where('id', $request->parent_id)
                    ->where('post_id', $post->id)
                    ->first();

                if (! $parentComment) {
                    return back()->with('error', 'Invalid reply comment.');
                }
            }

            Comment::create([
                'post_id' => $post->id,
                'user_id' => $userId,
                'comment' => $request->input('comment'),
                'parent_id' => $request->parent_id ?? null,
            ]);

            return back()->with('success', 'Comment added.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Unable to add comment right now.');
        }
    }

    public function storeFeedReply(Request $request, FeedPost $post, Comment $comment)
    {
        $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        if ((int) $comment->post_id !== (int) $post->id) {
            return back()->with('error', 'Invalid reply target.');
        }

        try {
            Comment::create([
                'post_id' => $post->id,
                'user_id' => Auth::id(),
                'comment' => $request->input('comment'),
                'parent_id' => $comment->id,
            ]);

            return back()->with('success', 'Reply added.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Unable to add reply right now.');
        }
    }

    // public function destroyFeedPost(FeedPost $post)
    // {
    //     if ((int) $post->user_id !== (int) Auth::id()) {
    //         return back()->with('error', 'You are not allowed to delete this post.');
    //     }

    //     try {
    //         $post->load('media');

    //         foreach ($post->media as $media) {
    //             if (
    //                 $media->file_path &&
    //                 ! filter_var($media->file_path, FILTER_VALIDATE_URL) &&
    //                 File::exists(public_path($media->file_path))
    //             ) {
    //                 File::delete(public_path($media->file_path));
    //             }
    //         }

    //         $post->delete();

    //         return back()->with('success', 'Post deleted successfully.');
    //     } catch (\Throwable $th) {
    //         return back()->with('error', 'Unable to delete post right now.');
    //     }
    // }
    public function destroyFeedPost(FeedPost $post)
    {
        if ((int) $post->user_id !== (int) Auth::id()) {
            return back()->with('error', 'You are not allowed to delete this post.');
        }

        try {
            $sharedPosts = FeedPost::where('shared_post_id', $post->id)->get();

            foreach ($sharedPosts as $sharedPost) {

                $sharedPost->delete();
            }

            $post->load('media');
            foreach ($post->media as $media) {
                if (
                    $media->file_path &&
                    ! filter_var($media->file_path, FILTER_VALIDATE_URL) &&
                    File::exists(public_path($media->file_path))
                ) {
                    File::delete(public_path($media->file_path));
                }
            }

            $post->delete();

            return back()->with('success', 'Post and its shares deleted successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Unable to delete post right now.');
        }
    }

    public function showPost(Request $request, $id)
    {
        $post = FeedPost::findOrFail($id);

        if ($request->ajax()) {
            return view('user.show_post', compact('post'));
        }

        return redirect()->route('user.feed', [
            'view-memories' => $id,
            'ref' => Str::random(15),
        ]);
    }

    public function shareToMyFeed($id)
    {
        $post = FeedPost::findOrFail($id);
        $user = Auth::user();
        $member = FamilyMember::where('user_id', $user->id)->select('family_id')->first();

        if (! $member || ! $member->family_id) {
            throw new \RuntimeException('Family profile is missing.');
        }
        $originalPostId = $post->shared_post_id ? $post->shared_post_id : $post->id;

        FeedPost::create([
            'user_id' => auth()->id(),
            'shared_post_id' => $originalPostId,
            'content' => null,
            'privacy' => 'public',
            'family_id' => $member->family_id,
            'type' => 'text',
        ]);

        return back()->with('success', 'Shared to your feed!');
    }
}
