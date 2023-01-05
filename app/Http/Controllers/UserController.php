<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function login()
    {
        return view('users.login');
    }

    public function logout(Request $request)
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function authenticate(Request $request)
    {
        $formFields = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);

        if (auth()->attempt($formFields)) {
            $request->session()->regenerate();
            return redirect('/');
        }
        return back()->withErrors(['email' => 'Invalid credentials!'])->onlyInput('email');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'department' => 'required'
        ]);

        $existingEmail = User::where('email', $request->email)->first();
        if ($existingEmail->email != Auth::user()->email) {
            $request->validate(['email' => 'unique:users']);
        }

        User::where('id', Auth::user()->id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'department' => $request->department
        ]);

        return back()->with('alert', 'Changes has been saved!');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'current_password' => 'required',
            'password' => ['required', 'min:8', 'max:255', 'string', 'confirmed'],
        ]);

        if (!Hash::check($request->password, Auth::user()->password) && Hash::check($request->current_password, Auth::user()->password)) {
            User::where('id', Auth::user()->id)->update([
                'password' => Hash::make($request->password)
            ]);
            return back()->with('alert', 'Your password has been changed!');
        } else if (!Hash::check($request->current_password, Auth::user()->password))
            return back()->with("alert", "Current password does not match!");
        else return back()->with('alert', 'New password cannot be the same as your old password!');
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate(['photo' => 'required']);

        $user['photo'] = $request->file('photo')->store('photos', 'public');
        User::where('id', Auth::user()->id)->update($user);

        return back()->with('alert', 'Photo has been uploaded!');
    }
}
