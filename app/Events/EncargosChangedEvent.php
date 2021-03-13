<?php
/**
 * File name: EncargosChangedEvent.php
 * Last modified: 2020.05.06 at 10:12:53
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EncargosChangedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $oldStatus;

    public $updatedEncargos;

    /**
     * EncargosChangedEvent constructor.
     * @param $oldEncargos
     * @param $updatedEncargos
     */
    public function __construct($oldStatus, $updatedEncargos)
    {
        $this->oldStatus = $oldStatus;
        $this->updatedEncargos = $updatedEncargos;
    }


}
