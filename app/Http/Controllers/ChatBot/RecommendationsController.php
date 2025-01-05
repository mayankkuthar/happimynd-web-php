<?php

namespace App\Http\Controllers\ChatBot;

use Illuminate\Http\Request;
use App\Models\UserProfile;
use App\Models\ChatBot\Recommendation;
use App\Models\ChatBot\RecommendationCategory;
use App\Http\Controllers\Controller;

class RecommendationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $recommendations = Recommendation::with(['recommendationCategory', 'userProfile'])->get();
        return view("Backend.chat-bot.recommendations.index")->with('recommendations', $recommendations);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user_profiles = UserProfile::all();
        $recommendationCategories = RecommendationCategory::all();

        return view("Backend.chat-bot.recommendations.create", compact('user_profiles', 'recommendationCategories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_profile_id'               => 'required',
            'recommendation_category_id'    => 'required',
            'title_1'                       => 'required',
            'url_1'                         => 'required',
            'title_2'                       => 'required',
            'url_2'                         => 'required',
            'title_3'                       => 'required',
            'url_3'                         => 'required',
        ], $request->only([
            'user_profile_id',
            'recommendation_category_id',
            'title_1',
            'url_1',
            'title_2',
            'url_2',
            'title_3',
            'url_3',
        ]));

        if ($validated) {
            Recommendation::create($validated);
            return redirect()->route('admin.chat-bot.recommendations.index')->with('success', 'Recommendations created successfully.');
        }

        return redirect()->back()->with('error', 'Unable to create.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Recommendation  $recommendation
     * @return \Illuminate\Http\Response
     */
    public function show(Recommendation $recommendation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Recommendation  $recommendation
     * @return \Illuminate\Http\Response
     */
    public function edit(Recommendation $recommendation)
    {
        $user_profiles = UserProfile::all();
        $recommendationCategories = RecommendationCategory::all();

        return view("Backend.chat-bot.recommendations.edit", [
            'user_profiles' => $user_profiles,
            'recommendation' => $recommendation,
            'recommendationCategories' => $recommendationCategories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Recommendation  $recommendation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Recommendation $recommendation)
    {
        $validated = $request->validate([
            'user_profile_id'               => 'required',
            'recommendation_category_id'    => 'required',
            'title_1'                       => 'required',
            'url_1'                         => 'required',
            'title_2'                       => 'required',
            'url_2'                         => 'required',
            'title_3'                       => 'required',
            'url_3'                         => 'required',
        ], $request->only([
            'user_profile_id',
            'recommendation_category_id',
            'title_1',
            'url_1',
            'title_2',
            'url_2',
            'title_3',
            'url_3',
        ]));

        if ($validated) {
            $recommendation = $recommendation->update($validated);
            return redirect()->route('admin.chat-bot.recommendations.index')->with('success', 'Recommendations created successfully.');
        }

        return redirect()->back()->with('error', 'Unable to create.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Recommendation  $recommendation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Recommendation $recommendation)
    {
        $recommendation->delete();
        return redirect()->route('admin.chat-bot.recommendations.index')->with('success', 'Recommendations deleted successfully.');
    }
}
