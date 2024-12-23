<?php

namespace App\Models;

use App\Observers\ProcessedObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([ProcessedObserver::class])]
class Image extends Model
{
    use HasFactory;

    public function processedImages(): HasMany
    {
        return $this->hasMany(ProcessedImage::class, 'images_id');
    }
}
