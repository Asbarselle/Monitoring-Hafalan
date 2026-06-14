<?php

namespace App\Events;

use App\Models\Hafalan;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HafalanCreated
{
    use Dispatchable, SerializesModels;

    public $hafalan;

    /**
     * Create a new event instance.
     */
    public function __construct(Hafalan $hafalan)
    {
        $this->hafalan = $hafalan;
    }
}
