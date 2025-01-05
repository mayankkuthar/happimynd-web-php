<?php

namespace App\Http\Controllers\ChatBot;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\ChatBot\ChatBotQuestion;
use App\Models\ChatBot\ChatBotCategory;
use App\Models\ChatBot\ChatBotOption;
use App\Http\Controllers\Controller;

class ChatBotQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions  = ChatBotQuestion::whereHas('category')->get();
        return view('Backend.chat-bot.assessments.questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = ChatBotCategory::all();
        return view('Backend.chat-bot.assessments.questions.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'chat_bot_category_id' => 'required',
            'question' => 'required',
            'options.*' => 'required',
        ]);

        $question = ChatBotQuestion::create($request->only(['chat_bot_category_id', 'question']));

        $options = collect($request->options)->map(function ($option) {
            return new ChatBotOption($option);
        });

        $question->options()->saveMany($options->all());

        return redirect()->route('admin.chat-bot.questions.index')->with('success', 'Question created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ChatBotQuestion  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(ChatBotQuestion $question)
    {
        $categories = ChatBotCategory::all();
        return view('Backend.chat-bot.assessments.questions.edit', compact('categories', 'question'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ChatBotQuestion  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ChatBotQuestion $question)
    {
        $validated = $this->validate($request, [
            'chat_bot_category_id' => 'required',
            'question' => 'required',
            'options.*' => 'required',
        ]);

        $question->update($validated);

        collect($request->options)->each(function ($option) use ($question) {
            $question->options()->updateOrCreate(Arr::only($option, 'id'), Arr::except($option, 'id'));
        });

        return redirect()->route('admin.chat-bot.questions.index')->with('success', 'Question updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ChatBotQuestion  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(ChatBotQuestion $question)
    {
        // Delete the associated options along with the question.
        $question->options()->delete();
        $question->delete();

        return redirect()->route('admin.chat-bot.questions.index')->with('success', 'Question deleted successfully!');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        if ($request->isMethod('get')) {
            $categories = ChatBotCategory::all();
            return view('Backend.chat-bot.assessments.questions.import', compact('categories'));
        }

        $rules = [
            'chat_bot_category_id' => 'required',
            'questions' => 'required|mimes:json',
        ];

        $messages = [
            'chat_bot_category_id.required' => 'The chat bot category is required.',
            'questions.required' => 'The questions file is required.',
            'questions.mimes' => 'The questions file must be of type: json',
        ];

        // Validate the request
        $this->validate($request, $rules, $messages);

        // Get file data
        $chatBotCategoryId = $request->input('chat_bot_category_id');
        $chatBotQuestions = json_decode($request->file('questions')->get(), true);

        collect($chatBotQuestions)->each(function ($chatBotQuestion) use ($chatBotCategoryId) {
            ChatBotCategory::findOrFail($chatBotCategoryId)
                ->questions()
                ->create(Arr::except($chatBotQuestion, 'options'))
                ->options()->createMany(Arr::get($chatBotQuestion, 'options'));
        });

        return back()->with('success', 'Questions uploaded successfully!');
    }
}
