<?php

namespace App\Mail;

use Log;

use Carbon\Carbon;

use App\Models\Contract;

use App\Helpers\PdfGenerator;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContractAdded extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, PdfGenerator;

    public $contractId;

    public function __construct(int $id)
    {
        $this->contractId = $id;
    }

    public function build()
    {
        $item = Contract::find($this->contractId);
        $contracts = $item->status;

        $fileName = $this->generatePdf([
            'contracts' => $contracts,
            'date' => $contracts->first()->created_at,
            'egn' => $item->value,
            'only_active' => true
        ]);

        Log::info('Sending email "нов договор" to '.$item->createdBy->email);
        
        return $this->subject('Нов договор '.$item->value)
            ->markdown('emails.contract_added',[
                'item' => $item,
                'user' => $item->createdBy
            ])
            ->attach(storage_path($fileName), [
                'as' => $item->value.'_'.Carbon::parse($item->last_check_on)->format('dmY'),
                'mime' => 'application/pdf',
            ]);
    }
}

