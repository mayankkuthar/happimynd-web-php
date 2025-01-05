<?php

namespace App\Http\Controllers\ChatBot;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ChatBot\ChatBotCategory;
use App\Models\ChatBot\ChatBotReportCharacteristic;

class ChatBotReportCharacteristicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories  = ChatBotCategory::with('reportCharacteristics')->get();
        return view('Backend.chat-bot.assessments.report-characteristics.index', ['categories' => $categories]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = ChatBotCategory::all();
        return view('Backend.chat-bot.assessments.report-characteristics.create', compact('categories'));
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
            'scores.*' => 'required',
        ]);

        $chatBotCategory = ChatBotCategory::find($request->chat_bot_category_id);

        $scores = collect($request->scores)->map(function ($score) {
            return new ChatBotReportCharacteristic($score);
        });

        $chatBotCategory->reportCharacteristics()->saveMany($scores->all());

        return redirect()->route('admin.chat-bot.report-characteristics.index')->with('success', 'Report characteristics created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ChatBotReportCharacteristic  $reportCharacteristic
     * @return \Illuminate\Http\Response
     */
    public function edit(ChatBotReportCharacteristic $reportCharacteristic)
    {
        $categories = ChatBotCategory::all();
        return view('Backend.chat-bot.assessments.report-characteristics.edit', compact('categories', 'reportCharacteristic'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ChatBotReportCharacteristic  $reportCharacteristic
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ChatBotReportCharacteristic $reportCharacteristic)
    {
        $this->validate($request, [
            'chat_bot_category_id' => 'required',
            'minimum' => 'required',
            'maximum' => 'required',
            'interpretation' => 'required',
        ]);

        $reportCharacteristic->update($request->only([
            'chat_bot_category_id',
            'minimum',
            'maximum',
            'interpretation',
        ]));

        return redirect()->route('admin.chat-bot.report-characteristics.index')->with('success', 'Report characteristic updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ChatBotReportCharacteristic  $reportCharacteristic
     * @return \Illuminate\Http\Response
     */
    public function destroy(ChatBotReportCharacteristic $reportCharacteristic)
    {
        $reportCharacteristic->delete();

        return redirect()->route('admin.chat-bot.report-characteristics.index')->with('success', 'Report characteristic deleted successfully!');
    }
}
