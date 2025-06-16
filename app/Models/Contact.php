<?php

namespace App\Models;

use Lyra\Database\Model;

class Contact extends Model {
    protected array $fillable = [
        'name',
        'phone_number',
        'user_id',
    ];
}