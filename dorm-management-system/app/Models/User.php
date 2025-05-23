<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_id',
        'email',
        'password',
        'role', // Добавлено поле роли

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Проверяет, является ли пользователь администратором.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'manager';
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id');
    }
    // app/Models/User.php

    public function acceptedBooking()
    {
        return $this->hasOne(Booking::class)
            ->whereIn('status', ['accepted', 'accepted_change'])
            ->latest(); // Берем последнюю принятую заявку
    }

    public function employee(){
        return $this->hasOne(Employee::class);
    }
    public function student()
    {
        return $this->hasOne(Student::class);
    }
    public function ads()
    {
        return $this->hasMany(Ad::class, 'user_id');
    }





}
