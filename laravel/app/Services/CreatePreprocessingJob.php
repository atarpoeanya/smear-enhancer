<?php

namespace App\Services;

use App\Jobs\ProcessImage;
use Illuminate\Support\Facades\Bus;

class CreatePreprocessingJob
{
    public function createJob(string $id, string $path, string $model, int $episode_len): void
    {

        Bus::chain([
            new ProcessImage($id, $path, $model, $episode_len),
        ])->dispatch();
    }
}
