<?php

namespace App\Http\Controllers\ChatBot;

use Illuminate\Http\Request;
use App\Models\ChatBot\DiscussionTopic;
use App\Http\Controllers\Controller;

class DiscussionTopicsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $discussionTopics = DiscussionTopic::get();
        return view('Backend.chat-bot.discussion-topics.index')->with('discussionTopics', $discussionTopics);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("Backend.chat-bot.discussion-topics.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate(['description' => 'required'], $request->only(['description']));

        if ($validated) {
            DiscussionTopic::create($validated);
            return redirect()->route('admin.chat-bot.discussion-topics.index')->with('success', 'Discussion topic created successfully.');
        }

        return redirect()->back()->with('error', 'Unable to create discussion topic.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DiscussionTopic  $discussionTopic
     * @return \Illuminate\Http\Response
     */
    public function show(DiscussionTopic $discussionTopic)
    {
        return redirect()->route('admin.chat-bot.discussion-topics.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DiscussionTopic  $discussionTopic
     * @return \Illuminate\Http\Response
     */
    public function edit(DiscussionTopic $discussionTopic)
    {
        return view('Backend.chat-bot.discussion-topics.edit')->with('discussionTopic', $discussionTopic);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DiscussionTopic  $discussionTopic
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DiscussionTopic $discussionTopic)
    {
        $validated = $request->validate(['description' => 'required'], $request->only(['description']));

        if ($validated) {
            $discussionTopic->update($validated);
            return redirect()->route('admin.chat-bot.discussion-topics.index')->with('status', 'Discussion topic updated successfully.');
        }

        return redirect()->back()->with('error', 'Unable to update discussion topic.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DiscussionTopic  $discussionTopic
     * @return \Illuminate\Http\Response
     */
    public function destroy(DiscussionTopic $discussionTopic)
    {
        $discussionTopic->delete();
        return redirect()->back()->with('status', 'Discussion topic deleted successfully.');
    }
}
