<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\QuizAssignment;
use Illuminate\Http\Request;

class QuizAssignmentController extends Controller
{
    // List all assignments
    public function index(Request $request)
{
    $query = QuizAssignment::with(['student', 'quiz']);

    // If there is a search term, filter by student name or lastname
    if ($request->has('search')) {
        $search = $request->input('search');
        $query->whereHas('student', function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('lastname', 'like', "%{$search}%");
        });
    }

    return $query->latest()->paginate(10);
}

    // Create new assignment
   public function store(Request $request)
{
    try {
        // 1. Validate for duplicates
        $request->validate([
            'student_id' => [
                'required',
                'exists:users,id',
                // This checks: "In quiz_assignments, does student_id already exist where quiz_id is X?"
                \Illuminate\Validation\Rule::unique('quiz_assignments')->where(function ($query) use ($request) {
                    return $query->where('student_id', $request->student_id)
                                 ->where('quiz_id', $request->quiz_id);
                }),
            ],
            'active' => 'required|integer',
        ], [
            // Custom error message
            'student_id.unique' => 'This student is already assigned to this quiz.'
        ]);

        // 2. If validation passes, create the record
        $assignment = new \App\Models\QuizAssignment();
        $assignment->student_id = $request->student_id;
        $assignment->active = $request->active;
        $assignment->quiz_id = $request->quiz_id; 
        $assignment->save();

        return response()->json(['message' => 'Created successfully!', 'data' => $assignment], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Return a 422 with the specific "Duplicate" message
        return response()->json(['errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Database error', 'details' => $e->getMessage()], 500);
    }
}

    // Show single assignment
    public function show(QuizAssignment $quizAssignment)
    {
        return $quizAssignment->load(['student','quiz']);
    }

    // Update assignment
    public function update(Request $request, QuizAssignment $quizAssignment)
    {
        $quizAssignment->update($request->all());
        return response()->json($quizAssignment);
    }

    // Delete assignment
    public function destroy(QuizAssignment $quizAssignment)
    {
        $quizAssignment->delete();
        return response()->noContent();
    }
}

