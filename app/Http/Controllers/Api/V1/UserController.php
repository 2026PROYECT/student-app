<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // List all students (role = student)
   public function index(Request $request)
{
    $query = User::where('role', 'student');

    if ($request->has('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('lastname', 'like', "%{$search}%")
              ->orWhere('surname', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    return $query->paginate(10);
}



    // Create a new user (student or admin)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'lastname'   => 'nullable|string|max:255',
            'surname'    => 'nullable|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6',
            'role'       => 'required|in:admin,student',
            'picture'    => 'nullable|image|max:2048',
            'saga_code'  => 'nullable|string|max:255',
            'id_number'  => 'nullable|string|max:255',
            'career'     => 'nullable|string|max:255',
            'semester'   => 'nullable|integer|min:1',
            'is_active'  => 'boolean',
        ]);

        if ($request->hasFile('picture')) {
            $validated['picture'] = $request->file('picture')->store('pictures', 'public');
        }

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    // Show a single user
    public function show(User $student)
    {
        return $student;
    }

    // Update an existing user
    public function update(Request $request, User $student)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'lastname'   => 'nullable|string|max:255',
            'surname'    => 'nullable|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $student->id,
            'password'   => 'nullable|string|min:6',
            'role'       => 'required|in:admin,student',
            'picture'    => 'nullable|image|max:2048',
            'saga_code'  => 'nullable|string|max:255',
            'id_number'  => 'nullable|string|max:255',
            'career'     => 'nullable|string|max:255',
            'semester'   => 'nullable|integer|min:1',
            'is_active'  => 'boolean',
        ]);

        if ($request->hasFile('picture')) {
            $validated['picture'] = $request->file('picture')->store('pictures', 'public');
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $student->update($validated);

        return response()->json($student);
    }

    // Delete a user
    public function destroy(User $student)
    {
        $student->delete();
        return response()->json(null, 204);
    }
}
