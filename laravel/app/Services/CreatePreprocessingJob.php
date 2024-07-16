<?php

namespace App\Services;

use App\Jobs\ProcessImage;

class CreatePreprocessingJob
{
    public function createJob(string $id, string $path,string $model, int $episode_len): void
    {
        ProcessImage::dispatch($id, $path, $model, $episode_len);
    }
}
