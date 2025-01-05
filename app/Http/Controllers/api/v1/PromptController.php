<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;

use App\Models\Prompt;

class PromptController extends Controller
{


    public function promptList()
    {
        $prompts = Prompt::get();
        return response()->json(['status' => 'success', 'message' => 'Prompts retrieved successfully.', 'list' => $prompts]);
    }
}
