<?php

namespace App\Http\Controllers\Student;

use App\Models\Category;
use App\Models\Document;
use App\Models\Employee;
use App\Models\GymBooking;
use App\Models\Payment;
use App\Models\Recovery;
use App\Models\Request as RepairRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Ad;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Building;

class StudentController extends Controller
{
    public function dashboard()
    {
        $newsList = News::orderBy('created_at', 'desc')->take(5)->get();
        $buildings = Building::all();
        $ads= Ad::latest()->get();
        $categories = Category::all();
        return view('student.dashboard', compact('newsList', 'buildings', 'ads', 'categories'));
    }
    public function personal()
    {
        $employees = Employee::with('user')->get();
        $requests = RepairRequest::where('user_id', auth()->id())->get();
        $newsList = News::orderBy('created_at', 'desc')->take(5)->get();
        $buildings = Building::all();
        $recoveries = Recovery::where('user_id', auth()->id())->get();
        $documents = Document::all();
        $user = auth()->user();
        $payments = Payment::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $balance = Payment::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        $booking = GymBooking::where('user_id', Auth::id())->first();
        return view('student.personal', compact('newsList','buildings', 'booking', 'employees', 'requests','documents','recoveries', 'payments', 'balance'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();

        if ($request->phone) {
            $user->phone = $request->phone;
        }

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('avatars', 'public');
            $user->photo = $path;
        }

        $user->save();

        return redirect()->route('student.personal')
            ->with('successType', 'profile_updated')
            ->with('success', 'Профиль обновлен!');
    }

    public function profile()
    {
        return view('student.profile');
    }
    public function update(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'phone'                 => 'nullable|string|max:255',
            'photo'                 => 'nullable|image|max:2048',
            'current_password'      => 'required_with:new_password',
            'new_password'          => 'nullable|confirmed|min:6',
        ]);

        if ($request->filled('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Текущий пароль введён неверно.']);
            }
            $user->password = Hash::make($request->new_password);
        }
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $user->photo = $path;
        }
        $user->save();

        return back()->with('success', 'Данные успешно обновлены!');
    }

}
