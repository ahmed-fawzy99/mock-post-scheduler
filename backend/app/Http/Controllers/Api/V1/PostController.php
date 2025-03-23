<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Filters\Api\V1\Filters\PostFilter;
use App\Http\Requests\Api\V1\PostRequest;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Post;
use App\Policies\PostPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PostController extends ApiController
{
    protected $policyClass = PostPolicy::class;
    public function allIndex(PostFilter $filters)
    {
        try {
            $this->isAble('viewAny', Post::class);
            return PostResource::collection(Post::filter($filters)->with('platforms')->orderBy('created_at', 'desc')->paginate());
        }
        catch (AuthorizationException $e) {
            return $this->error('You are not authorized to view posts', null, 403);
        }
        catch (\Exception $e) {
            return $this->error('Posts retrieval error', null, 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(PostFilter $filters)
    {
        try {
            $this->isAble('view', Post::class);

//            Gate::authorize('view', Post::class);

            return PostResource::collection(Post::filter($filters)->with('platforms')->where('user_id', auth()->id())->orderBy('created_at', 'desc')->paginate());
        }
        catch (AuthorizationException $e) {
            return $this->error('You are not authorized to view posts', null, 403);
        }
        catch (\Exception $e) {
            return $this->error('Posts retrieval error', null, 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        try {
//            $this->isAble(['create-posts'], Post::class);
            $this->isAble('create', Post::class);

            $post = Post::create([
                'user_id' => auth()->id(),
                'title' => $request->title,
                'content' => $request['content'], // $request->content throws an IDE error
                'scheduled_at' => $request->scheduled_at,
                'status' => strtoupper($request->status),
            ]);
            $post->platforms()->attach($request->platforms);

            if ($request->hasFile('image')) {
                $img = $post->addMedia($request->file('image'))->toMediaCollection('post_image');
                $post->update(['image_url' => $img->getUrl()]);
            }
            return $this->ok('Post created successfully', new PostResource($post));
        }
        catch (AuthorizationException $e) {
            return $this->error('You are not authorized to create posts', null, 403);
        }
        catch (\Exception $e) {
            return $this->error('Post creation error', null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $post_id)
    {
        try {
            $post = Post::findOrFail($post_id);
            $this->isAble('view', $post);
//            $this->isAble(['view-posts', 'view-own-posts'], $post);
            return $this->ok('Post', new PostResource($post->load('platforms')));
        }
        catch (ModelNotFoundException $e) {
            return $this->error('Post not found', null, 404);
        }
        catch (AuthorizationException $e) {
            return $this->error('You are not authorized to see this post', null, 403);
        }
        catch (\Exception $e) {
            return $this->error('Post Error', null, 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, int $post_id)
    {
        try {
            $post = Post::findOrFail($post_id);
            $this->isAble('update', $post);


            $data = array_filter([
                'title' => $request->title,
                'content' => $request['content'],
                'scheduled_at' => $request->scheduled_at,
                'status' => strtoupper($request->status),
            ], fn($value) => !is_null($value));

            $image_url = null;
            if ($request->hasFile('image')) {
                if ($post->getFirstMedia('post_image')) {
                    // Delete the old image
                    $post->getFirstMedia('post_image')->delete();
                }
                $img = $post->addMedia($request->file('image'))->toMediaCollection('post_image');
                $image_url = $img->getUrl();
            } elseif ($request->image_url) {
                $image_url = $request->image_url;
            }

            if (!$image_url && $post->getFirstMedia('post_image')) {
                $post->getFirstMedia('post_image')->delete();
            }

            $data['image_url'] = $image_url;

            if ($data['status'] !== 'SCHEDULED') {
                $data['scheduled_at'] = null;
            }

            $post->update($data);
            $post->platforms()->sync($request->platforms);
            return $this->ok('Post updated successfully', new PostResource($post));
        }
        catch (ModelNotFoundException $e) {
            return $this->error('Post not found', null, 404);
        }
        catch (AuthorizationException $e) {
            return $this->error('You are not authorized to update this post', null, 403);
        }
        catch (\Exception $e) {
            return $this->error('Post update failed', null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $post_id)
    {
        try {
            $post = Post::findOrFail($post_id);
            $this->isAble('delete', $post);
            $post->delete();
            return $this->ok('Post deleted successfully', null);
        }
        catch (ModelNotFoundException $e) {
            return $this->error('Post not found', null, 404);
        }
        catch (AuthorizationException $e) {
            return $this->error('You are not authorized to delete this post', null, 403);
        }
        catch (\Exception $e) {
            return $this->error('Post deletion failed', null, 500);
        }
    }
}
