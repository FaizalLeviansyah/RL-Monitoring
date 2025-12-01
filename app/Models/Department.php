<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $connection = 'mysql_master';
    protected $table = 'tbl_department';
    protected $primaryKey = 'department_id';

    protected $fillable = ['department_name', 'company_id'];

    public function company() {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}
