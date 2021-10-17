<?php

namespace App\Mail;

use App\Models\Contract;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContractChanged extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $contractId;

    public function __construct(int $id)
    {
        $this->contractId = $id;
    }

    public function build()
    {
        $item = Contract::find($this->contractId);
        
        return $this->subject('Промени по договор '.$item->value)
            ->markdown('emails.contract_changed',[
                'item' => $item,
                'user' => $item->createdBy
            ]);
    }
}
