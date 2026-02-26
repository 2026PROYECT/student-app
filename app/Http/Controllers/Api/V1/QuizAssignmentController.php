<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\QuizAssignment;
use Illuminate\Http\Request;

class QuizAssignmentController extends Controller
{
    // List all assignments
    public function index()
    {
        return QuizAssignment::with(['student','quiz'])->paginate(10);
    }

    // Create new assignment
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'quiz_id'    => 'nullable|exists:quizzes,id',
            'finished'   => 'boolean',
            'score'      => 'nullable|integer',
        ]);

        $assignment = QuizAssignment::create($validated);

        return response()->json($assignment, 201);
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

