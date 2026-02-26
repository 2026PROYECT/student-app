<?php

namespace App\Services;

use App\Models\QuestionBank;

class QuestionService
{
    public function allPaginate($request, $perPage)
    {
        $orderColumn = request('order_column', 'created_at');
        $orderDirection = request('order_direction', 'desc');

        if (!in_array($orderColumn, ['id', 'quiz_id', 'created_at'])) {
            $orderColumn = 'created_at';
        }
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        return QuestionBank::query()
            ->when($request->quiz_id, function ($query) use ($request) {
                $query->where('quiz_id', $request->quiz_id);
            })
            ->with(['quiz:id,title'])
            ->orderBy($orderColumn, $orderDirection)
            ->paginate($perPage);
    }

    public function all($request)
    {
        return QuestionBank::query()
            ->when($request->quiz_id, function ($query) use ($request) {
                $query->where('quiz_id', $request->quiz_id);
            })
            ->select('id', 'question1', 'question2', 'question3')
            ->get();
    }

    public function store($request)
    {
        $data = $request->validated();

        // Handle file uploads
        if ($request->hasFile('picture')) {
            $data['picture'] = $request->file('picture')->store('questions/pictures', 'public');
        }
        if ($request->hasFile('sound')) {
            $data['sound'] = $request->file('sound')->store('questions/sounds', 'public');
        }

        return QuestionBank::create([
            'quiz_id'      => $data['quiz_id'],
            'question1'    => $data['question1'],
            'question2'    => $data['question2'] ?? null,
            'question3'    => $data['question3'] ?? null,
            'picture'      => $data['picture'] ?? null,
            'sound'        => $data['sound'] ?? null,
            'option_a'     => $data['option_a'],
            'option_b'     => $data['option_b'],
            'option_c'     => $data['option_c'],
            'option_d'     => $data['option_d'],
            'right_answer' => $data['right_answer'],
            'area'         => $data['area'] ?? null,
        ]);
    }

    public function update($request, $id)
    {
        $data = $request->validated();
        $question = $this->show($id);

        if ($request->hasFile('picture')) {
            $data['picture'] = $request->file('picture')->store('questions/pictures', 'public');
        }
        if ($request->hasFile('sound')) {
            $data['sound'] = $request->file('sound')->store('questions/sounds', 'public');
        }

        $question->update([
            'quiz_id'      => $data['quiz_id'],
            'question1'    => $data['question1'],
            'question2'    => $data['question2'] ?? null,
            'question3'    => $data['question3'] ?? null,
            'picture'      => $data['picture'] ?? $question->picture,
            'sound'        => $data['sound'] ?? $question->sound,
            'option_a'     => $data['option_a'],
            'option_b'     => $data['option_b'],
            'option_c'     => $data['option_c'],
            'option_d'     => $data['option_d'],
            'right_answer' => $data['right_answer'],
            'area'         => $data['area'] ?? null,
        ]);

        return $question;
    }

    public function show($id)
    {
        return QuestionBank::findOrFail($id);
    }

    public function delete($request, $id)
    {
        return $this->show($id);
    }
}