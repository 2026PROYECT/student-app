<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Http\Resources\UserResource;


class UserController extends Controller
{
    /**
     * Display a listing of students with search and pagination.
     */
    public function index(Request $request)
    {
        try {
            return User::query()
                ->where('role', 'student')
                ->when($request->search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('lastname', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('career_id', 'like', "%{$search}%");
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
            return UserResource::collection(
    User::query()
        ->where('role', 'student')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
);


        }
    }

    public function students()
{
    return User::where('role', 'student')->get();
}
    /**
     * Store a newly created user (student or admin).
     */
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
            'career_id'  => 'required|exists:careers,id_career',
            'semester'   => 'required|integer|min:1|max:10',
            'is_active'  => 'boolean',
            'is_admin'   => 'boolean',
        ]);

        if ($request->hasFile('picture')) {
            $validated['picture'] = $request->file('picture')->store('pictures', 'public');
        }

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        return new UserResource($user);
        return response()->json($user, 201);
    }

    /**
     * Display the specified student.
     */
    public function show(User $student)
    {
        return response()->json($student);
        return new UserResource($student);
    }

    /**
     * Update the specified student in storage.
     */
   public function update(Request $request, User $student)
{
    $validated = $request->validate([
        'name'       => 'required|string|max:255',
        'lastname'   => 'nullable|string|max:255',
        'surname'    => 'nullable|string|max:255',
        'email'      => [
            'required',
            'email',
            Rule::unique('users')->ignore($student->id),
        ],
        'password'   => 'nullable|string|min:6',
        'role'       => 'required|in:admin,student',
        // ✅ picture is optional
        'picture'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'saga_code'  => 'nullable|string|max:255',
        'id_number'  => 'nullable|string|max:255',
        'career_id'  => 'required|exists:careers,id_career',
        'semester'   => 'required|integer|min:1|max:10',
        'is_active'  => 'boolean',
        'is_admin'   => 'boolean',
    ]);

    // ✅ Only update picture if a new file was uploaded
    if ($request->hasFile('picture')) {
        $validated['picture'] = $request->file('picture')->store('pictures', 'public');
    } else {
        $validated['picture'] = $student->picture; // keep existing
    }

    // ✅ Only update password if provided
    if (!empty($validated['password'])) {
        $validated['password'] = Hash::make($validated['password']);
    } else {
        unset($validated['password']);
    }

    $student->update($validated);

    return new UserResource($student);
}


    /**
     * Remove the specified student from storage.
     */
    public function destroy(User $student)
    {
        $student->delete();
        return response()->json(null, 204);
    }
}