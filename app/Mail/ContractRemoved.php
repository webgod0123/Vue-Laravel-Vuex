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

class ContractRemoved extends Mailable implements ShouldQueue
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
        
        $fileName = $this->generatePdf([
            'contracts' => [],
            'date' => $item->last_check_on,
            'egn' => $item->value,
            'only_active' => true
        ]);

        Log::info('Sending email "Прекратяване на договор" to '.$item->createdBy->email);

        return $this->subject('Прекратяване на договор '.$item->value)
            ->markdown('emails.contract_removed',[
                'item' => $item,
                'user' => $item->createdBy
            ])
            ->attach(storage_path($fileName), [
                'as' => $item->value.'_'.Carbon::parse($item->last_check_on)->format('dmY'),
                'mime' => 'application/pdf',
            ]);    
    }
}
