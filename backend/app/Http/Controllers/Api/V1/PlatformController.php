<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Filters\Api\V1\Filters\PlatformFilter;
use App\Http\Requests\Api\V1\PlatformRequest;
use App\Http\Resources\Api\V1\PlatformResource;
use App\Models\Platform;
use App\Policies\PlatformPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PlatformController extends ApiController
{
    protected $policyClass = PlatformPolicy::class;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->isAble('viewAny', Platform::class);
        try {
            if (auth()->user()) {
                if ($request->query('all', false) === 'true')
                    return PlatformResource::collection(Platform::all());
                return PlatformResource::collection(Platform::currentUserAllowedPlatforms());
            }
            return PlatformResource::collection(Platform::all());
        }
        catch (\Exception $e) {
            return $this->error('Platforms retrieval error', null, 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(PlatformRequest $request)
    {
        try {
            $this->isAble('create', Platform::class);

            $platform = Platform::create([
                'name' => $request->name,
                'type' => $request->type,
                'character_limit' => $request->character_limit,
            ]);

            return $this->ok('Platform created successfully', new PlatformResource($platform));
        }
        catch (AuthorizationException $e) {
            return $this->error('You are not authorized to create platforms', null, 403);
        }
        catch (\Exception $e) {
            return $this->error('Platform creation error', null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $platform_id, PlatformFilter $filters)
    {
        try {
            $platform = Platform::filter($filters)->findOrFail($platform_id);
            $this->isAble('view', $platform);
            return $this->ok('Platform', new PlatformResource($platform));
        }
        catch (ModelNotFoundException $e) {
            return $this->error('Platform not found', null, 404);
        }
        catch (AuthorizationException $e) {
            return $this->error('You are not authorized to see this platform', null, 403);
        }
        catch (\Exception $e) {
            \Log::info($e->getMessage());
            return $this->error('Platform Error', null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlatformRequest $request, int $platform_id)
    {
        try {
            $platform = Platform::findOrFail($platform_id);
            $this->isAble('update', $platform);

            $platform->update([
                'name' => $request->name,
                'type' => $request->type,
                'character_limit' => $request->character_limit,
            ]);

            return $this->ok('Platform updated successfully', new PlatformResource($platform));
        }
        catch (ModelNotFoundException $e) {
            return $this->error('Platform not found', null, 404);
        }
        catch (AuthorizationException $e) {
            return $this->error('You are not authorized to update this platform', null, 403);
        }
        catch (\Exception $e) {

            return $this->error('Platform update failed', null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $platform_id)
    {
        try {
            $platform = Platform::findOrFail($platform_id);
            $this->isAble('delete', $platform);
            $platform->delete();
            return $this->ok('Platform deleted successfully', null);
        }
        catch (ModelNotFoundException $e) {
            return $this->error('Platform not found', null, 404);
        }
        catch (AuthorizationException $e) {
            return $this->error('You are not authorized to delete this platform', null, 403);
        }
        catch (\Exception $e) {
            return $this->error('Platform deletion failed', null, 500);
        }
    }
}
