<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash; // Import Hash

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(15);
        return response()->json($users, Response::HTTP_OK);
    }

    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($user, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|string|max:255|unique:bookai_users,username',
            'email' => 'required|string|email|max:255|unique:bookai_users,email',
            'password' => 'required|string|min:8|confirmed',
            'full_name' => 'nullable|string|max:255',
            'avatar_url' => 'nullable|url|max:255',
            'bio' => 'nullable|string',
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);
        $user = User::create($validatedData);

        return response()->json($user, Response::HTTP_CREATED);
    }

    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validate([
            'username' => 'sometimes|required|string|max:255|unique:bookai_users,username,' . $id,
            'email' => 'sometimes|required|string|email|max:255|unique:bookai_users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'full_name' => 'nullable|string|max:255',
            'avatar_url' => 'nullable|url|max:255',
            'bio' => 'nullable|string',
            'status' => 'sometimes|required|string|in:active,inactive,suspended',
        ]);

        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }
        $user->update($validatedData);

        return response()->json($user, Response::HTTP_OK);
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $user->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}