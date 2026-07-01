<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipment';

    protected $primaryKey = 'equipment_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'equipment_id',
        'equipment_name',
        'category_id',
        'lab_id',
        'total_quantity',
        'available_quantity',
        'status',
        'purchase_date'
    ];
}