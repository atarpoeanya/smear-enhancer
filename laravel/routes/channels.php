<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('image.{id}', function () {
    return true;
});
