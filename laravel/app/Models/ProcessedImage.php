<?php

namespace App\Models;

use App\Observers\ProcessedObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProcessedImage extends Model
{
    use HasFactory;

}
