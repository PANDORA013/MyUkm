<?php

namespace App\Http\Controllers;

use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private ProfileService $profileService
    ) {}

    public function show(): View
    {
        $user = Auth::user();
        $data = $this->profileService->getProfileViewData($user);
        
        return view($data['view_name'], array_merge([
            'user' => $data['user'],
            'memberships' => $data['memberships']
        ], $data['layout_data']));
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:current_password', Password::defaults()],
        ]);

        $user = Auth::user();
        $result = $this->profileService->updatePassword($user, $validated['password']);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->withErrors(['error' => $result['message']]);
    }

    public function updatePhoto(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'photo' => ['required', 'image', 'max:2048', 'mimes:jpeg,png,jpg']
        ]);

        $user = Auth::user();
        $result = $this->profileService->updatePhoto($user, $request->file('photo'));

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->withErrors(['photo' => $result['message']]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $result = $this->profileService->deleteAccount($user);

        if ($result['success']) {
            // Logout user and invalidate session
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')->with('status', $result['message']);
        }

        return back()->withErrors(['error' => $result['message']]);
    }
}
