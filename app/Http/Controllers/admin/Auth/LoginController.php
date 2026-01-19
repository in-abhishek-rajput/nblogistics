<?php

namespace App\Http\Controllers\admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class LoginController extends Controller
{

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Find user by mobile
        $user = User::where('mobile', $request->mobile)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'mobile' => ['The provided credentials do not match our records.'],
            ]);
        }

        // Check if user is active
        if (isset($user->is_active) && $user->is_active == 'N') {
            $errors = new MessageBag(['mobile' => ['Your account is deactivated. Please contact the administrator.']]);
            return redirect()->back()->withErrors($errors);
        }

        // Log the user in
        Auth::login($user, $request->filled('remember'));

        // Update login tracking if fields exist
        if (isset($user->logins) || isset($user->last_login_ip) || isset($user->last_login_at)) {
            $updateData = [];
            
            if (isset($user->logins)) {
                $updateData['logins'] = ($user->logins ?? 0) + 1;
            }
            
            if (isset($user->last_login_ip)) {
                $updateData['last_login_ip'] = $request->getClientIp();
            }
            
            if (isset($user->last_login_at)) {
                $updateData['last_login_at'] = Carbon::now()->toDateTimeString();
            }
            
            if (!empty($updateData)) {
                $user->update($updateData);
            }
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
