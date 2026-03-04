<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\QuizAssignment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuizAssignmentController extends Controller
{
    // 1. List only activated students
    public function index(Request $request)
    {
        // Removed .quiz relationship
        $query = QuizAssignment::with(['student']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate(10);
    }

    // 2. Activate a student (Create record)
    public function store(Request $request)
    {
        try {
            $request->validate([
                'student_id' => [
                    'required',
                    'exists:users,id',
                    // Changed: Now we just check if the student is ALREADY in the active list
                    Rule::unique('quiz_assignments', 'student_id'),
                ],
                'active' => 'required|integer',
            ], [
                'student_id.unique' => 'This student is already on the active list.'
            ]);

            $assignment = new QuizAssignment();
            $assignment->student_id = $request->student_id;
            $assignment->active = $request->active;
            // Removed $assignment->quiz_id
            $assignment->save();

            return response()->json(['message' => 'Student activated successfully!', 'data' => $assignment], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Database error', 'details' => $e->getMessage()], 500);
        }
    }

    // 3. Show single activation
    public function show(QuizAssignment $quizAssignment)
    {
        // Removed .quiz relationship
        return $quizAssignment->load(['student']);
    }

    // 4. Update status (Enable/Disable)
    public function update(Request $request, QuizAssignment $quizAssignment)
    {
        // Ensure we don't accidentally try to save quiz_id if it's in the request
        $quizAssignment->update($request->only(['active', 'student_id']));
        
        return response()->json($quizAssignment);
    }

    // 5. Deactivate/Remove student from list
    public function destroy(QuizAssignment $quizAssignment)
    {
        $quizAssignment->delete();
        return response()->noContent();
    }
}
