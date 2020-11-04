<?php

namespace App\Http\Controllers;

use App\Http\Requests\Preference\UpdateRequest;
use App\Models\Preference;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PreferenceController extends Controller
{
    public function index()
    {
        return Inertia::render('General/Preference');
    }

    public function store(UpdateRequest $request)
    {
        Preference::query()->updateOrCreate(['user_id' => Auth::id()], $request->validated());

        return redirect()->back()->with('success', __('notification.success.update', ['model' => 'Pengaturan']));
    }
}
