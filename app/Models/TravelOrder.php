<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelOrder extends Model
{
    use HasFactory;

    /**
     */
    protected $connection = 'second_connection';

    /**
     */
    protected $table = 'orders';

    /**
     */
    protected $fillable = [
        'user_id',
        'requester_name',
        'destination',
        'departure_date',
        'return_date',
        'status',
    ];

    /**
     */
    protected $dates = [
        'departure_date',
        'return_date',
        'created_at',
        'updated_at',
    ];

    /**
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
