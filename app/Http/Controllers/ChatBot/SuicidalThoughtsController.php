<?php

namespace App\Http\Controllers\ChatBot;

use Illuminate\Http\Request;
use App\Models\ChatBot\SuicidalThought;
use App\Http\Controllers\Controller;

class SuicidalThoughtsController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if ($request->isMethod('GET')) {
            $suicidalThought = SuicidalThought::first();

            return view('Backend.chat-bot.suicidal-thought-help-message')->with('suicidalThought', $suicidalThought);
        }

        if ($request->isMethod('POST')) {
            $validated = $request->validate(['description' => 'required'], $request->only(['description']));

            if ($validated) {
                $suicidalThought = SuicidalThought::firstOrCreate(['description' => 'string']);
                $suicidalThought->update($validated);
            }

            return redirect()->back()->with('success', 'Suicidal thought help message update successfully.');
        }
    }
}
