<?php

namespace App\Http\Controllers\ChatBot;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ChatBot\ChatBotCategory;
use Illuminate\Support\Arr;

class ChatBotCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories  = ChatBotCategory::all();
        return view('Backend.chat-bot.assessments.categories.index', ['categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $this->validate($request, [
            'name' => 'required',
            'calculation_step_macro' => 'required',
        ]);

        ChatBotCategory::create($validated);
        return redirect()->route('admin.chat-bot.categories.index')->with('success', 'Category created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ChatBot\ChatBotCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(ChatBotCategory $category)
    {
        return view('Backend.chat-bot.assessments.categories.edit', ['category' => $category]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ChatBot\ChatBotCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ChatBotCategory $category)
    {
        $validated = $this->validate($request, [
            'name' => 'required',
            'calculation_step_macro' => 'required',
        ]);

        $category->update($validated);
        return redirect()->route('admin.chat-bot.categories.index')->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ChatBot\ChatBotCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(ChatBotCategory $category)
    {
        $category->delete();
        return redirect()->back()->with('success', 'Category deleted successfully!');
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
            return view('Backend.chat-bot.assessments.categories.import');
        }

        $rules = [
            'categories' => 'required|mimes:json',
        ];

        $messages = [
            'categories.required' => 'The categories file is required.',
            'categories.mimes' => 'The categories file must be of type: json',
        ];

        // Validate the request
        $this->validate($request, $rules, $messages);

        // Get file data
        $categories = json_decode($request->file('categories')->get(), true);

        collect($categories)->each(function ($category) {
            ChatBotCategory::create(Arr::except($category, 'report_characteristics'))
                ->reportCharacteristics()
                ->createMany(Arr::get($category, 'report_characteristics'));
        });

        return back()->with('success', 'Categories uploaded successfully!');
    }
}
