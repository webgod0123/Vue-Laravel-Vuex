<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Excel;
use Artisan;

use Carbon\Carbon;

use App\Http\Imports\ContractsImport;
use App\Models\Contract;

use Illuminate\Http\Request;

use App\Exceptions\BadRequestException;
use App\Models\ContractLastStatus;

class ContractController extends Controller
{
    public function getRegular(Request $POST)
    {     
        $pagination = $POST['pagination'];
        $searchString = $POST['filter'];

        $page = $pagination['page'];
        $itemsPerPage = $pagination['itemsPerPage'];
        $sortBy = !empty($pagination['sortBy']) ? $pagination['sortBy'][0] : '';
        $descending = !empty($pagination['sortByDesc']) ? $pagination['sortByDesc'][0] : false;

        $query = Contract::with(['status','last','createdBy' => function($q) use($sortBy,$descending){
            if($sortBy === 'created_by') {
                if($descending) {
                    $q->orderByDesc('name');
                } else {
                    $q->order('name');
                }
            }
        }]);
        
        $user = Auth::user();
        if($user->is_admin === 0) {
            $query->where('created_by',$user->id);
        }

        if($searchString && strlen($searchString) > 3) {
            $query->where(function($q)use($searchString){
                $q->orWhere('value', 'LIKE', '%'.$searchString.'%');
                $q->orWhere('case_number', 'LIKE', '%'.$searchString.'%');
            });
        }

        $total = $query->count();

        if($sortBy === 'value' || $sortBy === 'case_number' || $sortBy === 'created_at' || $sortBy === 'last_check_on') {
            if($descending) {
                $query->orderByDesc($sortBy);
            } else {
                $query->orderBy($sortBy);
            }
        } else if($sortBy === 'status') {
            if($descending) {
                $query->orderByDesc('change');
            } else {
                $query->orderBy('change');
            }
        }

        $page = (int)$page - 1;
        $query->skip($itemsPerPage * $page);
        $query->take($itemsPerPage);

        return [
            'total' => $total,
            'items' => $query->get()
        ];
    }

    public function acknowledge(Request $POST)
    {
        $contract = Contract::query()->with(['status','createdBy'])->findOrFail($POST['contract_id']);
        $contract->change = null;
        $contract->acknowledged_by = Auth::user()->id;
        $contract->save();

        $contract->last()->delete();
        $contract->last()->saveMany(array_map(function($c) {
            return new ContractLastStatus($c);
        },$contract->status->toArray()));
        
        $contract->last;

        return $contract;
    }

    public function addRegular(Request $POST)
    {
        $egn = $POST['egn'];
        $caseNumber = $POST['case_number'];

        if(!$egn) {
            throw new BadRequestException('Полето ЕГН/ЕИК е задължително');
        }

        $isEGN = strlen($egn) === 10;
        if($isEGN) {
            $type = 'EGN';
        } else {
            $type = 'Bulstat';
        }

        if(Contract::where('value',$POST['egn'])->first() !== null) {
            throw new BadRequestException('Това ЕГН/Булсат вече се проверява');
        }

        $contract = new Contract();
        $contract->type = $type;
        $contract->value = $egn;
        $contract->case_number = $caseNumber;
        $contract->created_by = Auth::user()->id;
        $contract->created_at = Carbon::now();
        $contract->save();

        $contract->status;
        $contract->createdBy;

        return $contract;
    }

    public function deleteRegular(Request $POST)
    {
        $id = $POST['id'];
        DB::transaction(function()use($id){
            $contract = Contract::find($id);
            $contract->status()->delete();
            $contract->last()->delete();
            $contract->delete();
        });
    }

    public function uploadExcel(Request $POST)
    {
        $fileData = $POST->file('file');
        $originalFileName = trim($fileData->getClientOriginalName());
        $raw = explode('.',$originalFileName);
        $extenion = end($raw);

        $filePath = $fileData->storeAs('/', 'temp_upload.'.$extenion);

        Excel::import(new ContractsImport, $filePath);
    }

    public function forceScan(Request $POST) 
    {
        if($POST['check_all']) {
            Artisan::queue('contract:check-all all');
        } else {
            Artisan::queue('contract:check-all');
        }
    }
}
