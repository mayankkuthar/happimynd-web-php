<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromptSaveRequest;
use App\Models\Plan;
use App\Models\Prompt;
use Illuminate\Http\Request;

class PromptController extends Controller
{
    public function __construct()
    {
        //
    }

    public function showPrompts()
    {
        $prompts = Prompt::get();

        return view('Backend.prompt.all')->with('prompts', $prompts);
    }

    public function showPromptsForm()
    {
        $prompts = Plan::get();

        return view("Backend.prompt.add")->with('prompts', $prompts);
    }

    public function storePrompts(PromptSaveRequest $request)
    {
        $request->validated();
        $prompt = new Prompt();

        if (isset($request['description'])) {
            $prompt->description = $request['description'];
            $prompt->save();

            return redirect()->back()->with('success', 'Prompt created Successfully to view <a href="' . route('admin.prompt.show') . '"> click here</a>');
        } else {
            return redirect()->back()->with('error', 'unable to create');
        }
    }

    public function editPrompts($id)
    {
        $prompt = Prompt::findOrFail($id);

        return view("Backend.prompt.edit")->with('prompt', $prompt);
    }

    public function updatePrompts(Request $request, $id)
    {
        $prompt = Prompt::find($id);

        if ($prompt && isset($request['description'])) {
            $prompt->description = $request['description'];
            $prompt->save();

            return redirect()->back()->with('status', 'updated Successfully');
        } else {
            return redirect()->back()->with('error', 'unable to update');
        }
    }

    public function deletePrompts(Request $request)
    {
        if (isset($request['id'])) {
            $result = Prompt::destroy($request['id']);

            if ($result) {
                return redirect()->back()->with('status', 'deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'unable to delete');
            }
        } else {
            return redirect()->back()->with('error', 'unable to delete');
        }
    }
}
