<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Building;
use App\Models\News;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ManagerUserController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:student,manager,admin'
        ]);

        User::create([
            'name' => $request->name,
            'user_id' => $request->user_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);


        return redirect()->route('manager.dashboard')
            ->with('successType', 'user_created');
    }

    public function index(Request $request)
    {
        $users = User::query()->whereIn('role', ['student', 'employee'])
            ->when($request->filled('filter_id'), fn($q) => $q->where('user_id', 'like', '%' . $request->filter_id . '%'))
            ->when($request->filled('filter_name'), fn($q) => $q->where('name', 'like', '%' . $request->filter_name . '%'))
            ->when($request->filled('filter_email'), fn($q) => $q->where('email', 'like', '%' . $request->filter_email . '%'))
            ->when($request->filled('filter_role'), fn($q) => $q->where('role', 'like', '%' . $request->filter_role . '%'))
            ->get();

        $newsList  = News::orderBy('created_at', 'desc')->take(5)->get();
        $buildings = Building::all();
        $requests  = Booking::where('status', 'pending')->get();

        session()->flash('successType', 'user_searched');
        session()->flash('success', 'Пользователь найден!');

        return view('manager.dashboard', compact('users','newsList','requests','buildings'));

    }
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->role === 'student') {
                // Находим данные студента
                if ($student = $user->student) {
                    // Находим комнату
                    $room = Room::find($student->room_id);
                    if ($room) {
                        // Если студент последний в комнате
                        if ($room->occupied_places <= 1) {
                            // Удаляем комнату
                            $room->delete();
                        } else {
                            // Иначе просто уменьшаем количество занятых мест
                            $room->decrement('occupied_places');
                        }
                    }
                    // Удаляем студента
                    $student->delete();
                }
                // Удаляем пользователя
                $user->delete();

                return redirect()->route('manager.dashboard')
                    ->with('successType', 'student_expelled')
                    ->with('success', 'Студент был отчислен из общежития, и его комната была удалена');
            } else {
                // Для остальных пользователей - просто удаляем
                $user->delete();
                return redirect()->route('manager.dashboard')
                    ->with('successType', 'user_deleted')
                    ->with('success', 'Пользователь был удален');
            }
        } catch (\Exception $e) {
            return redirect()->route('manager.dashboard')
                ->with('error', 'Произошла ошибка при удалении: ' . $e->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'user_id' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048'
        ]);

        $user->user_id = $request->user_id;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('avatars', 'public');
            $user->photo = $path;
        }

        $user->save();

        return redirect()->route('manager.dashboard')
            ->with('successType', 'user_updated')
            ->with('success', 'Данные пользователя обновлены!');
    }


    public function getUserJson($id)
    {
        $user = User::with(['acceptedBooking.building', 'acceptedBooking.room', 'employee'])
            ->where('id', $id)
            ->first();

        if (!$user) {
            return response()->json(['error' => 'Пользователь не найден'], 404);
        }

        $userData = [
            'id' => $user->id,
            'user_id' => $user->user_id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'photo' => $user->photo,
            'role' => $user->role,
            'accepted_booking' => null,
            'employee_details' => null
        ];

        // Если это студент и есть активное бронирование
        if ($user->role === 'student' && $user->acceptedBooking) {
            $booking = $user->acceptedBooking;
            $userData['accepted_booking'] = [
                'building' => [
                    'name' => $booking->building->name,
                    'address' => $booking->building->address,
                ],
                'room' => [
                    'floor' => $booking->room->floor,
                    'room_number' => $booking->room->room_number
                ]
            ];
        }

        // Если это работник
        if ($user->role === 'employee' && $user->employee) {
            $userData['employee_details'] = [
                'position' => $user->employee->position,
                'department' => $user->employee->department,
                // Добавьте другие необходимые поля работника
            ];
        }

        return response()->json($userData);
    }
}
