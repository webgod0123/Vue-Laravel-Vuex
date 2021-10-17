<?php 

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model 
{
    
    protected $table = 'contracts';
    protected $fillable = [ 'type', 'value', 'case_number','change', 'acknowledged_by', 'created_by', 'created_at', 'last_check_on' ];
    public $timestamps = false;

    public function status()
    {
        return $this->hasMany(ContractCurrentStatus::class,'contract_id','id');
    }

    public function last()
    {
        return $this->hasMany(ContractLastStatus::class,'contract_id','id');
    }

    public function createdBy()
    {
        return $this->hasOne(User::class,'id','created_by');
    }
}