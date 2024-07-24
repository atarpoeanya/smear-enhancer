<?php

namespace App\Services;

use App\Jobs\ProcessImage;
use Illuminate\Support\Facades\Bus;

class CreatePreprocessingJob
{
    public function createJob(string $id, string $original_path, string $model, int $episode_len, bool $has_raw): void
    {

        Bus::chain([
            new ProcessImage($id, $original_path, $model, $episode_len, $episode_len, $has_raw),
        ])->dispatch();
    }
}
