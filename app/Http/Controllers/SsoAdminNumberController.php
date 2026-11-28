<?php

namespace App\Http\Controllers;

use App\Models\SsoAdminNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Admin Panel: Admins > App Admin Numbers.
 * Mobile numbers that get admin rights in Quizzard and the Games Hub when
 * their owner signs in there with HappiMynd (OTP). Checked on every sign-in,
 * so removing or switching off a number takes the rights away.
 */
class SsoAdminNumberController extends Controller
{
    public function index()
    {
        return view('Backend.sso_admin_numbers', [
            'numbers' => SsoAdminNumber::orderBy('name')->get(),
            'orgs' => $this->quizzardOrgs(),
        ]);
    }

    public function create()
    {
        return view('Backend.sso_admin_number_edit', [
            'number' => new SsoAdminNumber(['quizzard_role' => 'super_admin', 'hub_admin' => true, 'is_active' => true]),
            'orgs' => $this->quizzardOrgs(),
        ]);
    }

    public function store(Request $request)
    {
        $number = new SsoAdminNumber();
        $this->fill($number, $request);
        $number->save();

        return redirect()->route('admin.ssoAdminNumbers')->with('success', $number->name . ' (' . $number->mobile . ') added.');
    }

    public function edit($id)
    {
        return view('Backend.sso_admin_number_edit', [
            'number' => SsoAdminNumber::findOrFail($id),
            'orgs' => $this->quizzardOrgs(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $number = SsoAdminNumber::findOrFail($id);
        $this->fill($number, $request);
        $number->save();

        return redirect()->route('admin.ssoAdminNumbers')->with('success', $number->name . ' (' . $number->mobile . ') updated.');
    }

    public function toggle($id)
    {
        $number = SsoAdminNumber::findOrFail($id);
        $number->is_active = !$number->is_active;
        $number->save();

        return back()->with('success', $number->name . ' switched ' . ($number->is_active ? 'on' : 'off') . '.');
    }

    public function destroy($id)
    {
        $number = SsoAdminNumber::findOrFail($id);
        $number->delete();

        return redirect()->route('admin.ssoAdminNumbers')->with('success', $number->name . ' (' . $number->mobile . ') removed.');
    }

    private function fill(SsoAdminNumber $number, Request $request)
    {
        $request->merge(['mobile' => SsoAdminNumber::normalize($request->input('mobile'))]);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'mobile' => ['required', 'digits:10', Rule::unique('sso_admin_numbers', 'mobile')->ignore($number->id)],
            'quizzard_role' => ['required', Rule::in(array_keys(SsoAdminNumber::QUIZZARD_ROLES))],
            'quizzard_org_id' => 'nullable|string|max:255|required_if:quizzard_role,admin',
            'hub_admin' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ], [
            'mobile.digits' => 'Enter a 10-digit mobile number.',
            'mobile.unique' => 'This number is already on the list.',
            'quizzard_org_id.required_if' => 'Pick the organisation this Quizzard admin manages.',
        ]);

        $number->fill([
            'name' => $data['name'],
            'mobile' => $data['mobile'],
            'quizzard_role' => $data['quizzard_role'],
            'quizzard_org_id' => $data['quizzard_role'] === 'admin' ? ($data['quizzard_org_id'] ?? null) : null,
            'hub_admin' => (bool) ($data['hub_admin'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);
    }

    /** Quizzard organisations (assessment_api DB on the same MySQL server), id => name. */
    private function quizzardOrgs(): array
    {
        try {
            return DB::table('assessment_api.organizations')->orderBy('name')->pluck('name', 'id')->all();
        } catch (\Throwable $e) {
            Log::warning('Could not read Quizzard organisations: ' . $e->getMessage());

            return [];
        }
    }
}
