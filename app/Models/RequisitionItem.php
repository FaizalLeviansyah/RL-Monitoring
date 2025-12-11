<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitionItem extends Model
{
    protected $table = 'requisition_items';
    protected $guarded = ['id'];
    public function letter() {
        return $this->belongsTo(RequisitionLetter::class, 'rl_id', 'id');
    }
    public function supplyHistories() {
        return $this->hasMany(SupplyHistory::class, 'rl_item_id', 'id');
    }

    // Hitung berapa yang sudah diterima
    public function getSuppliedQtyAttribute()
    {
        return $this->supplyHistories->sum('qty_received');
    }

    // Hitung sisa yang belum datang
    public function getRemainingQtyAttribute()
    {
        return $this->qty - $this->supplied_qty;
    }

    protected $fillable = [
        'rl_id', 'item_name', 'qty', 'uom', 'description', 'status_item',
        // Tambahkan ini:
        'part_number', 'stock_on_hand'
    ];
}
