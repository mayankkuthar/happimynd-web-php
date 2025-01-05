<?php

namespace App\Http\Controllers\ChatBot;

use Illuminate\Http\Request;
use App\Models\ChatBot\RecommendationCategory;
use App\Http\Controllers\Controller;

class RecommendationCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $recommendationCategories = RecommendationCategory::get();
        return view('Backend.chat-bot.recommendation-categories.index', compact('recommendationCategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Backend.chat-bot.recommendation-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate(['name' => 'required'], $request->only(['name']));

        if ($validated) {
            RecommendationCategory::create($validated);
            return redirect()->route('admin.chat-bot.recommendation-categories.index')->with('status', 'Created successfully.');
        }

        return redirect()->back()->with('error', 'Unable to create.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RecommendationCategory  $recommendationCategory
     * @return \Illuminate\Http\Response
     */
    public function show(RecommendationCategory $recommendationCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RecommendationCategory  $recommendationCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(RecommendationCategory $recommendationCategory)
    {
        return view('Backend.chat-bot.recommendation-categories.edit', compact('recommendationCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RecommendationCategory  $recommendationCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RecommendationCategory $recommendationCategory)
    {
        $validated = $request->validate(['name' => 'required'], $request->only(['name']));

        if ($validated) {
            $recommendationCategory->update($validated);
            return redirect()->route('admin.chat-bot.recommendation-categories.index')->with('status', 'Updated successfully.');
        }

        return redirect()->back()->with('error', 'Unable to update.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RecommendationCategory  $recommendationCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(RecommendationCategory $recommendationCategory)
    {
        $recommendationCategory->delete();
        return redirect()->route('admin.chat-bot.recommendation-categories.index')->with('status', 'Deleted successfully.');
    }
}
