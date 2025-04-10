<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $table = 'repair_requests';

    protected $fillable = ['status', 'employee_id', 'date', 'type', 'description', ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }



}
