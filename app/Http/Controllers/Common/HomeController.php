<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WaitlistSubmission;
class HomeController extends Controller
{
    public function setPassword($id)
    {
        $user = User::where('email', $id)->firstOrFail();
        //check if user exists
        if(!$user) {
            return redirect()->route('login')->with('error', 'User with this email does not exist.');
        }
        if ($user->email_verified_at) {
            //return redirect()->route('login')->with('error', 'Email is already verified.');
        }
        $user->email_verified_at = now();
        $user->save();
        // change waitlist submission status to converted
        $submission = WaitlistSubmission::where('email', $id)->firstOrFail();
        if ($submission) {
            $submission->status = 'converted';
            $submission->save();
        }
        return view('common/generate-password', compact('user')); 
        // return redirect()->route('login')->with('success', 'Email verified successfully. Please login to your account.');
    }

    public function index()
    {
        //return view('home');
    }
    public function submitPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);
        $user = User::where('id', $id)->firstOrFail();
        if (!$user) {
            return redirect()->route('login')->with('error', 'User with this email does not exist.');
        }
        $user->password = bcrypt($request->password);
        $user->save();

        return redirect()->route('login')->with('success', 'Password set successfully. Please login to your account.');
    }
}
