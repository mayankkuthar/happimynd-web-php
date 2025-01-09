<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'reason' => 'required|string|max:255',
            'message' => 'nullable|string',
            'referral' => 'nullable|string|max:255',
        ]);

        DB::table('contacts')->insert($validated);

        return response()->json(['message' => 'Contact saved successfully.']);
    }

    public function index()
    {
        $contacts = DB::table('contacts')->get();
        return view('Backend.contact_lead', compact('contacts'));
    }

}
