<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    // Получение списка этажей в корпусе
    public function getFloors($building_id)
    {
        $floors = Room::where('building_id', $building_id)
            ->pluck('floor')
            ->unique()
            ->sort()
            ->values(); // Убираем лишние индексы

        return response()->json($floors);
    }


    // Получение списка свободных комнат на этаже
    public function getRooms($building_id, $floor) {
        $rooms = Room::where('building_id', $building_id)
            ->where('floor', $floor)
            ->whereColumn('occupied_places', '<', 'capacity')
            ->get(['id', 'room_number']);

        return response()->json($rooms);
    }

    // Сохранение заявки (студент отправляет форму)
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'building_id' => 'required|integer|exists:buildings,id',
                'floor'       => 'required|integer',
                'room_id'     => 'required|integer|exists:rooms,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            dd('Ошибка валидации', $e->errors());
        }


        $room = Room::findOrFail($request->room_id);

        if ($room->occupied_places >= $room->capacity) {
            return redirect()->back()->with('error', 'Комната уже заполнена!');
        }


        Booking::create([
            'user_id'     => Auth::id(),
            'building_id' => $request->building_id,
            'floor'       => $request->floor,
            'room_id'     => $request->room_id,
            'status'      => 'pending',
        ]);

        return redirect()->back()->with('success', 'Заявка на заселение отправлена!');
    }


    // Менеджер видит все заявки в своём общежитии
    public function indexForManager()
    {
        $requests = Booking::with(['user','building','room'])->whereIn('status', ['pending','pending_change'])
            ->get();

        return view('manager.requests', compact('requests'));
    }

    // Принять заявку
    public function accept($id)
    {
        $booking = Booking::findOrFail($id);
        $room = $booking->room;

        if ($room->occupied_places >= $room->capacity) {
            return redirect()->back()->with('error', 'Мест в комнате больше нет!');
        }

        DB::transaction(function () use ($booking, $room) {
            if ($booking->status === 'pending') {
                $booking->update(['status' => 'accepted']);
            } elseif ($booking->status === 'pending_change') {
                $booking->update(['status' => 'accepted_change']);
            }

            $room->update(['occupied_places' => $room->occupied_places + 1]);

            Booking::where('user_id', $booking->user_id)
                ->where('status', 'pending')
                ->where('id', '!=', $booking->id)
                ->update(['status' => 'rejected']);

            $booking->user->refresh();
        });

        return redirect()->back()->with('success', 'Заявка принята! Все другие заявки отклонены.');
    }


    // Отклонить заявку
    public function reject($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Заявка отклонена!');
    }
    public function changeRoom(Request $request)
    {
        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'floor'       => 'required|integer',
            'room_id'     => 'required|exists:rooms,id',
        ]);

        // Создаём заявку на «смену комнаты»,
        // можно завести поле типа (change_room) или status = 'pending_change' и т.д.
        Booking::create([
            'user_id'     => Auth::id(),
            'building_id' => $request->building_id,
            'floor'       => $request->floor,
            'room_id'     => $request->room_id,
            'status'      => 'pending',
        ]);

        return redirect()->back()->with('success', 'Заявка на смену комнаты отправлена!');
    }
}


