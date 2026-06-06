<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('requisition-channel', function ($user) {
    return true;
});
