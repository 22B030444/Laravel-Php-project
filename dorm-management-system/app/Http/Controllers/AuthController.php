<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function showLoginForm()
    {
        return view('auth.login');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

//        if (Auth::attempt($credentials))  {
//            return redirect()->route('admin.users.create'); // После входа перенаправляем на регистрацию юзера
//        }
        \Log::info('Попытка входа', ['email' => $request->email, 'password' => $request->password]);

        if (\Auth::attempt($credentials)) {

            $user = \Auth::user();

            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'manager':
                    return redirect()->route('manager.dashboard');
                case 'student':
                    return redirect()->route('student.dashboard');
                case 'employee':
                    return redirect()->route('employee.dashboard');
                default:
                    return redirect('/');
            }
        }

        return back()->withErrors(['email' => 'Неверные данные'])->withInput();
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}

