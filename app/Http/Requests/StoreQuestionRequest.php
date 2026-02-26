<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Allow all authenticated users (adjust if you need role-based checks)
        return true;
    }

    public function rules(): array
{
    return [
        'quiz_id'      => 'required|exists:quizzes,id',
        'question1'    => 'nullable|string',
        'question2'    => 'nullable|string',
        'question3'    => 'nullable|string',
        'picture'      => 'nullable|image|max:4048', 
        'sound'        => 'nullable|mimes:mp3,wav,aac,ogg|max:8120',
        'option_a'     => 'required|string',
        'option_b'     => 'required|string',
        'option_c'     => 'required|string',
        'option_d'     => 'required|string',
        'right_answer' => 'required|in:A,B,C,D',
        'area'         => 'required|string',
    ];
}

    public function messages(): array
    {
        return [
            'quiz_id.required'      => 'Quiz ID is required.',
            'quiz_id.exists'        => 'The selected quiz does not exist.',
            'option_a.required'     => 'Option A is required.',
            'option_b.required'     => 'Option B is required.',
            'option_c.required'     => 'Option C is required.',
            'option_d.required'     => 'Option D is required.',
            'right_answer.in'       => 'Right answer must be one of A, B, C, or D.',
            'picture.image'         => 'Picture must be a valid image file.',
            'sound.mimes'           => 'Sound must be an mp3 or wav file.',
        ];
    }
}

