<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $connection = 'mysql_master';
    protected $table = 'tbl_company';
    protected $primaryKey = 'company_id';

    protected $fillable = ['company_code', 'company_name', 'logo_path'];

    public function departments() {
        return $this->hasMany(Department::class, 'company_id', 'company_id');
    }
}
