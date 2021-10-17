<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractCurrentStatus extends Model 
{
    public $timestamps = false;
    protected $table = 'contract_current_status';
    protected $fillable = [ 
        'contract_id',
        'contractor_bulstat',
        'contractor_name',
        'individual_eik',
        'individual_names',
        'start_date',
        'last_amend_date',
        'end_date',
        'reason',
        'time_limit',
        'eco_code',
        'profession_code',
        'remuneration',
        'profession_name',
        'ekatte_code',
        'created_at'
    ];

    public function parseFromApi(array $data) {
        $this->contractor_bulstat = $data['ContractorBulstat'];
        $this->contractor_name = $data['ContractorName'];
        $this->individual_eik = $data['IndividualEIK'];
        $this->individual_names = $data['IndividualNames'];
        $this->start_date = $data['StartDate'];
        $this->last_amend_date = isset($data['LastAmendDate']) ? $data['LastAmendDate'] : '';
        $this->end_date = isset($data['EndDate']) ? $data['EndDate'] : '';
        $this->reason = isset($data['Reason']) ? $data['Reason'] : '';
        $this->time_limit = isset($data['TimeLimit']) ? $data['TimeLimit']: '';
        $this->eco_code = $data['EcoCode'];
        $this->profession_code = isset($data['ProfessionCode']) ? $data['ProfessionCode'] : '';
        $this->remuneration = $data['Remuneration'];
        $this->profession_name = $data['ProfessionName'];
        $this->ekatte_code = isset($data['EKATTECode']) ? $data['EKATTECode'] : '';
        return $this;
    }
}