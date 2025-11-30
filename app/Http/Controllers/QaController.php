<?php

namespace App\Http\Controllers;

use App\Models\QaQuestion;
use App\Models\QaAnswer;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QaController extends Controller
{
    public function index(Request $request)
    {
        $questions = QaQuestion::with(['user', 'category'])
            ->active()
            ->when($request->category, function($query) use ($request) {
                $query->byCategory($request->category);
            })
            ->when($request->filter === 'unanswered', function($query) {
                $query->unanswered();
            })
            ->when($request->filter === 'answered', function($query) {
                $query->answered();
            })
            ->when($request->sort === 'popular', function($query) {
                $query->popular();
            }, function($query) {
                $query->latest();
            })
            ->paginate(20);

        $categories = Category::where('type', 'qa')->get();

        return view('qa.index', compact('questions', 'categories'));
    }

    public function show($id)
    {
        $question = QaQuestion::with(['user', 'category', 'answers.user', 'acceptedAnswer'])
            ->findOrFail($id);

        $question->incrementViews();

        $answers = $question->answers()
            ->with('user')
            ->when(request('sort') === 'popular', function($query) {
                $query->popular();
            }, function($query) {
                $query->latest();
            })
            ->get();

        return view('qa.show', compact('question', 'answers'));
    }

    public function create()
    {
        $categories = Category::where('type', 'qa')->get();
        return view('qa.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|array|max:5',
            'tags.*' => 'string|max:50'
        ]);

        $question = QaQuestion::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category_id' => $validated['category_id'] ?? null,
            'tags' => $validated['tags'] ?? []
        ]);

        return redirect()->route('qa.show', $question->id)
            ->with('success', 'Question posted successfully!');
    }

    public function answer(Request $request, $id)
    {
        $question = QaQuestion::findOrFail($id);

        $validated = $request->validate([
            'content' => 'required|string'
        ]);

        $answer = QaAnswer::create([
            'question_id' => $question->id,
            'user_id' => auth()->id(),
            'content' => $validated['content']
        ]);

        $question->increment('answers_count');

        return redirect()->route('qa.show', $question->id)
            ->with('success', 'Answer posted successfully!');
    }

    public function acceptAnswer($questionId, $answerId)
    {
        $question = QaQuestion::where('id', $questionId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $answer = QaAnswer::where('id', $answerId)
            ->where('question_id', $questionId)
            ->firstOrFail();

        $question->acceptAnswer($answer);

        return back()->with('success', 'Answer accepted!');
    }

    public function vote(Request $request, $type, $id)
    {
        $validated = $request->validate([
            'vote_type' => 'required|in:up,down'
        ]);

        $model = $type === 'question' ? QaQuestion::findOrFail($id) : QaAnswer::findOrFail($id);

        if ($validated['vote_type'] === 'up') {
            \App\Models\Vote::upvote(auth()->user(), $model);
            $model->increment('votes_count');
        } else {
            \App\Models\Vote::downvote(auth()->user(), $model);
            $model->decrement('votes_count');
        }

        return back()->with('success', 'Vote recorded!');
    }
}
