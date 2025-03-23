<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Filters\Api\V1\Filters\UserFilter;
use App\Http\Requests\Api\V1\UserAdminUpdateRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Traits\Api\V1\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// This controller is for admins to manage users. Users' self-management is handled in AuthController
class UserController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    // ApiResponse trait used to unify API responses
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(UserFilter $filters)
    {
        try {
            $this->isAble('viewAny', User::class);
            return UserResource::collection(User::filter($filters)->orderBy('created_at', 'desc')->paginate());
        }
        catch (AuthorizationException $e) {
            return $this->error('You are not authorized to view users', null, 403);
        }
        catch (\Exception $e) {
            return $this->error('Post Creation Error', null, 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            $this->isAble('view', $user);
            return $this->ok('User', new UserResource($user));
        }
        catch (ModelNotFoundException $e) {
            return $this->error('User not found', null, 404);
        }
        catch (AuthorizationException $e) {
            return $this->error('You are not authorized to see this user', null, 403);
        }
        catch (\Exception $e) {
            return $this->error('User Error', null, 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UserAdminUpdateRequest $request, string $user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            $this->isAble('update', $user);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
            $user->disallowedPlatforms()->sync($request->disallowedPlatforms);

            // revoke session if user is updated
            DB::table('sessions')->where('user_id', $user->id)->delete();

            return $this->ok('User Updated', new UserResource($user));
        }
        catch (ModelNotFoundException $e) {
            return $this->error('User not found', null, 404);
        }
        catch (AuthorizationException $e) {
            return $this->error('You are not authorized to see this user', null, 403);
        }
        catch (\Exception $e){
            \Log::info($e->getMessage());
            return $this->error('User Error', null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            $this->isAble('delete', $user);
            $user->delete();
            return $this->ok('User deleted successfully', null);
        }
        catch (ModelNotFoundException $e) {
            return $this->error('User not found', null, 404);
        }
        catch (AuthorizationException $e) {
            return $this->error('You are not authorized to delete this user', null, 403);
        }
        catch (\Exception $e) {
            return $this->error('User deletion failed', null, 500);
        }
    }
}
