<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterItem extends Model
{
    protected $table = 'master_items';
    protected $guarded = ['id'];

    // Helper untuk pencarian hybrid nanti
    public function getFullLabelAttribute()
    {
        return "{$this->item_code} - {$this->item_name} ({$this->specification})";
    }
}
