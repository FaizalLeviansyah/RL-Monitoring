<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    // WAJIB ADA: Agar nembak ke database Master (Mas Hendri)
    protected $connection = 'mysql_master';

    protected $table = 'tbl_position';
    protected $primaryKey = 'position_id';

    protected $fillable = ['position_name', 'company_id'];

}
