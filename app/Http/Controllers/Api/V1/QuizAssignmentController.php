<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\QuizAssignment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuizAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = QuizAssignment::with('student');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate(10);
    }

    public function store(Request $request)
{
    $request->validate([
        'student_id' => [
            'required',
            Rule::exists('users', 'id')->where('role', 'student'), // ✅ only users with role=student
            'unique:quiz_assignments,student_id'
        ],
        'active' => 'required|boolean',
    ], [
        'student_id.unique' => 'Este estudiante ya tiene una asignación registrada.'
    ]);

    // ✅ Create the assignment
    $assignment = QuizAssignment::create([
        'student_id' => $request->student_id,
        'active' => $request->active,
    ]);

    return response()->json([
        'message' => 'Creado exitosamente!',
        'data' => $assignment
    ], 201);
}


public function show(QuizAssignment $quizAssignment)
{
    return response()->json($quizAssignment->load('student'));
}



    public function update(Request $request, QuizAssignment $quizAssignment)
    {
        $quizAssignment->update($request->only(['student_id','active']));
        return response()->json($quizAssignment);
    }

    public function destroy(QuizAssignment $quizAssignment)
    {
        $quizAssignment->delete();
        return response()->noContent();
    }
}


