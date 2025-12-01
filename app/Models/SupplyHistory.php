<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplyHistory extends Model
{
    protected $table = 'supply_histories';
    protected $guarded = ['id'];

    public function item() {
        return $this->belongsTo(RequisitionItem::class, 'rl_item_id', 'id');
    }

    // Relasi ke User Master (Penerima Barang)
    public function receiver() {
        return $this->belongsTo(User::class, 'received_by', 'employee_id');
    }
}
