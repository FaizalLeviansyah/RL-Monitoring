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
}
