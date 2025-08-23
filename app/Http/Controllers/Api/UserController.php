<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class UserController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/users",
     * tags={"Users"},
     * summary="Get list of users",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="q", in="query", required=false, @OA\Schema(type="string"), description="Search query for name, username, or email"),
     * @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", default=1)),
     * @OA\Parameter(name="limit", in="query", required=false, @OA\Schema(type="integer", default=10)),
     * @OA\Response(response=200, description="Users retrieved successfully", @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Users retrieved successfully."),
     * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
     * @OA\Property(property="pagination", ref="#/components/schemas/Pagination")
     * ))
     * )
     */
    public function index(Request $request)
    {
        $query = User::query()->where('is_admin', false);
    
        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
    
        $users = $query->paginate($request->input('limit', 10));
    
        return UserResource::collection($users)
            ->additional([
                'status' => 'success',
                'message' => 'Users retrieved successfully.'
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * @OA\Get(
     * path="/api/users/{id}",
     * tags={"Users"},
     * summary="Get a specific user by ID",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="User retrieved successfully", @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="User retrieved successfully."),
     * @OA\Property(property="data", ref="#/components/schemas/User")
     * )),
     * @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(User $user)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'User retrieved successfully.',
            'data' => new UserResource($user->loadCount('courses'))
        ]);
    }

    /**
     * @OA\Put(
     * path="/api/users/{id}",
     * tags={"Users"},
     * summary="Update a user",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="first_name", type="string"),
     * @OA\Property(property="last_name", type="string"),
     * @OA\Property(property="username", type="string"),
     * @OA\Property(property="email", type="string", format="email"),
     * @OA\Property(property="password", type="string", format="password", description="min: 8 characters")
     * )
     * ),
     * @OA\Response(response=200, description="User updated successfully", @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="User updated successfully."),
     * @OA\Property(property="data", ref="#/components/schemas/User")
     * )),
     * @OA\Response(response=403, description="Forbidden"),
     * @OA\Response(response=404, description="Not Found"),
     * @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, User $user)
    {
        if (Auth::id() == $user->id) {
            return response()->json(['status' => 'error', 'message' => 'Admin cannot update their own profile via this endpoint.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        $data = $validator->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully.',
            'data' => new UserResource($user)
        ]);
    }

   /**
     * @OA\Delete(
     * path="/api/users/{id}",
     * tags={"Users"},
     * summary="Delete a user",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=204, description="No Content"),
     * @OA\Response(response=403, description="Forbidden"),
     * @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(User $user)
    {
        if (Auth::id() == $user->id) {
            return response()->json(['status' => 'error', 'message' => 'Admin cannot delete themselves.'], 403);
        }

        $user->delete();
        return response()->json(null, 204);
    }

    /**
     * @OA\Post(
     * path="/api/users/{user}/balance",
     * tags={"Users"},
     * summary="Add balance to a user account",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"increment"},
     * @OA\Property(property="increment", type="number", format="float", example=50000)
     * )
     * ),
     * @OA\Response(response=200, description="Balance added successfully", @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="message", type="string", example="Balance added successfully."),
     * @OA\Property(property="data", ref="#/components/schemas/User")
     * )),
     * @OA\Response(response=404, description="User not found"),
     * @OA\Response(response=422, description="Validation error")
     * )
     */
    public function addBalance(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'increment' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }
        
        $user->increment('balance', $request->input('increment'));

        return response()->json([
            'status' => 'success',
            'message' => 'Balance added successfully.',
            'data' => new UserResource($user)
        ]);
    }
}
