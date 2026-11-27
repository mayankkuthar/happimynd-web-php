<?php

namespace App\Http\Controllers;

use App\Models\RewardRedeemOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Admin Panel: Engagement > Redeem Options. What users can redeem reward points
 * for; the app lists them from GET /api/v1/reward-redeem-options.
 */
class RewardRedeemOptionController extends Controller
{
    const IMAGE_FOLDER = 'reward-redeem-options';

    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 25);
        $perPage = ($perPage >= 1 && $perPage <= 200) ? $perPage : 25;

        $options = RewardRedeemOption::ordered()->paginate($perPage)->appends($request->except('page'));

        return view('Backend.reward_redeem_options', ['options' => $options]);
    }

    public function create()
    {
        return view('Backend.reward_redeem_option_edit', ['option' => new RewardRedeemOption(['is_active' => true, 'category' => 'discount'])]);
    }

    public function store(Request $request)
    {
        $option = new RewardRedeemOption();
        $this->fill($option, $request);
        $option->save();

        return redirect()->route('admin.rewardRedeemOptions')->with('success', 'Redeem option "' . $option->title . '" added.');
    }

    public function edit($id)
    {
        return view('Backend.reward_redeem_option_edit', ['option' => RewardRedeemOption::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $option = RewardRedeemOption::findOrFail($id);
        $this->fill($option, $request);
        $option->save();

        return redirect()->route('admin.rewardRedeemOptions')->with('success', 'Redeem option "' . $option->title . '" updated.');
    }

    public function toggle($id)
    {
        $option = RewardRedeemOption::findOrFail($id);
        $option->is_active = !$option->is_active;
        $option->save();

        return back()->with('success', '"' . $option->title . '" switched ' . ($option->is_active ? 'on' : 'off') . '.');
    }

    public function destroy($id)
    {
        $option = RewardRedeemOption::findOrFail($id);
        $option->delete();

        return redirect()->route('admin.rewardRedeemOptions')->with('success', 'Redeem option "' . $option->title . '" deleted.');
    }

    private function fill(RewardRedeemOption $option, Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'category' => ['required', Rule::in(array_keys(RewardRedeemOption::CATEGORIES))],
            'points_required' => 'required|integer|min:1|max:1000000',
            'reward_value' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_image' => 'nullable|boolean',
            'stock' => 'nullable|integer|min:0|max:1000000',
            'max_per_user' => 'nullable|integer|min:1|max:1000',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'terms' => 'nullable|string|max:5000',
            'sort_order' => 'nullable|integer|min:-1000|max:1000',
            'is_active' => 'nullable|boolean',
        ], [], [
            'points_required' => 'points needed',
            'max_per_user' => 'limit per user',
            'valid_until' => 'valid until',
        ]);

        $option->fill([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'category' => $data['category'],
            'points_required' => (int) $data['points_required'],
            'reward_value' => $data['reward_value'] ?? null,
            'stock' => isset($data['stock']) ? (int) $data['stock'] : null,
            'max_per_user' => isset($data['max_per_user']) ? (int) $data['max_per_user'] : null,
            'valid_from' => $data['valid_from'] ?? null,
            'valid_until' => $data['valid_until'] ?? null,
            'terms' => $data['terms'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        if ($request->hasFile('image')) {
            try {
                $path = Storage::disk('s3')->put(self::IMAGE_FOLDER, $request->file('image'));
                $option->image = Storage::disk('s3')->url($path);
            } catch (\Throwable $e) {
                Log::error('Redeem option image upload failed: ' . $e->getMessage());
                abort(back()->withInput()->with('error', 'The image could not be uploaded. Please try again.'));
            }
        } elseif (!empty($data['remove_image'])) {
            $option->image = null;
        }
    }
}
