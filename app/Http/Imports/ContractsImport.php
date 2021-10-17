<?php 

namespace App\Http\Imports;

use Auth;

use App\Models\Contract;

use App\Exceptions\BadRequestException;


use Illuminate\Support\Collection;

use Maatwebsite\Excel\Concerns\ToCollection;

class ContractsImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $currentContracts = Contract::get();

        $rows = $rows->filter(function($row) use($currentContracts) {
            if(trim($row[0]) == '') {
                return false;
            }
            if(!is_numeric($row[0]) || strlen($row[0]) < 9 || strlen($row[0]) > 10) {
                throw new BadRequestException('В колонка 1 са намерени са стойности, които не са ЕГН или Булстат');
            }
            if($currentContracts->where('value',$row[0])->first() === null) {
                return $row;
            }
        });

        $user = Auth::user();
        $insertData = [];
        foreach($rows as $row) {
            $insertData[] = [
                'type' => strlen($row[0]) === 10 ? 'EGN' : 'Bulstat',
                'value' => $row[0],
                'case_number' => $row[1],
                'created_by' => $user->id
            ];
        }

        Contract::insert($insertData);
    }
}