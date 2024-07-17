<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class ProcessedImage extends Model
{
    use HasFactory;

    protected $table = 'processed_image';

    public static function getOrderedImages(string $original_id): Collection {
        return self::where('images_id', $original_id)->oldest()->get();
    }
}
