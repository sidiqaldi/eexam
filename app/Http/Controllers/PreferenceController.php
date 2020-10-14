<?php

namespace App\Http\Controllers;

use App\Http\Requests\Preference\UpdateRequest;
use App\Models\Preference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreferenceController extends Controller
{
    public function update(UpdateRequest $request)
    {
        Preference::query()->updateOrCreate(['user_id' => Auth::id()], $request->validated());

        return redirect()->back()->with('success', __('notification.success.update', ['model' => 'Pengaturan']));
    }
}
