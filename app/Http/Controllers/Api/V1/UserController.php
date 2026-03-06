<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users based on role (Admin or Student).
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $role = $request->query('role', 'student'); // Defaults to student

        $users = User::where('role', $role)
            ->when($role === 'student', function ($query) {
                // Eager load academic data only for students
                $query->with(['student.career']);
            })
            ->when($search, function ($query, $search) use ($role) {
                $query->where(function ($q) use ($search, $role) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");

                    // Search academic fields only if we are filtering students
                    if ($role === 'student') {
                        $q->orWhereHas('student', function ($subQuery) use ($search) {
                            $subQuery->where('saga_code', 'like', "%{$search}%")
                                     ->orWhereHas('career', function ($careerQuery) use ($search) {
                                         $careerQuery->where('name', 'like', "%{$search}%");
                                     });
                        });
                    }
                });
            })
            ->latest()
            ->paginate(10);

        return response()->json($users);
    }

    /**
     * Store a newly created user (and student profile if applicable).
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'name'      => 'required|string|max:255',
        'lastname'  => 'required|string|max:255',
        'surname'   => 'nullable|string|max:255',
        'email'     => 'required|email|unique:users,email',
        'password'  => 'required|min:8',
        'role'      => 'required|in:admin,student',
        'career_id' => 'required_if:role,student|exists:careers,id',
        'semester'  => 'required_if:role,student|integer|between:1,10',
        'picture'   => 'nullable|image|max:2048',
        'saga_code' => 'nullable|string',
        'id_number' => 'nullable|string',
    ]);

    DB::beginTransaction();
    try {
        $picturePath = null;
        if ($request->hasFile('picture')) {
            $picturePath = $request->file('picture')->store('profiles', 'public');
        }

        $user = User::create([
            'name'     => $validated['name'],
            'lastname' => $validated['lastname'],
            'surname'  => $validated['surname'] ?? null,
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
            'picture'  => $picturePath,
        ]);

        // Wrap student creation and use $request directly to avoid "Undefined key" errors
        if ($user->role === 'student') {
            $user->student()->create([
                'career_id' => $request->career_id,
                'semester'  => $request->semester,
                'saga_code' => $request->saga_code,
                'id_number' => $request->id_number,
            ]);
        }

        DB::commit();
        return response()->json([
            'message' => 'User created successfully', 
            'user' => $user->load('student')
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        if ($picturePath) Storage::disk('public')->delete($picturePath);
        return response()->json([
            'message' => 'Error creating user', 
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Display the specified user with their profile.
     */
    public function show(User $student) // Variable name matches route parameter
    {
        return response()->json($student->load('student.career'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $student)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'surname'   => 'nullable|string|max:255',
            'email'     => ['required', 'email', Rule::unique('users')->ignore($student->id)],
            'career_id' => 'required_if:role,student|exists:careers,id',
            'semester'  => 'required_if:role,student|integer|between:1,10',
            'password'  => 'nullable|min:8', // Password optional on update
        ]);

        DB::beginTransaction();
        try {
            $student->update([
                'name'     => $validated['name'],
                'lastname' => $validated['lastname'],
                'surname'  => $validated['surname'],
                'email'    => $validated['email'],
            ]);

            if ($request->filled('password')) {
                $student->update(['password' => Hash::make($request->password)]);
            }

            if ($student->role === 'student') {
                $student->student()->updateOrCreate(
                    ['user_id' => $student->id],
                    [
                        'career_id' => $request->career_id,
                        'semester'  => $request->semester,
                        'saga_code' => $request->saga_code,
                        'id_number' => $request->id_number,
                    ]
                );
            }
            // Handle picture update
            if ($request->hasFile('picture')) {
                // Delete the old picture if it exists to save space
                if ($student->picture) {
                    Storage::disk('public')->delete($student->picture);
                }
                // Store new picture
                $picturePath = $request->file('picture')->store('profiles', 'public');
                $student->update(['picture' => $picturePath]);
            }
            DB::commit();
            return response()->json(['message' => 'User updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Update failed', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $student)
    {
        // OnDelete('cascade') in migration handles the Student table record
        $student->delete();
        return response()->json(null, 204);
    }
}   