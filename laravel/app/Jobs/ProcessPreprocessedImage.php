<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPreprocessedImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public PreprocessedImage $p_image, public string $model_used, public int $episode_len)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($episode_len > 0) {
            $command = escapeshellcmd('python '.'"'.base_path('python-script/script_test.py').'"'.' "'.$this->image->path.'"');

            $result = Process::run($command);

            $image = new Image();
            $image->path = $this->original_path.$imageName;
            $image->save();

            $result->successful();
        }

    }
}
