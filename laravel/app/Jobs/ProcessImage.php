<?php

namespace App\Jobs;

use App\Models\Image;
use App\Models\ProcessedImage;
use App\Services\SavePreprocessedImage;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Log\Logger;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class ProcessImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $id, public string $original_path, public string $model_path, public int $episode_len) {}

    /**
     * Execute the job.
     */
    public function handle(SavePreprocessedImage $savePreprocessedImage): void
    {

        $original_path = Storage::disk('original')->path($this->original_path);
        
        $command = escapeshellcmd('python '.'"'.base_path('python-script/blood-enhancer/test_b.py').'" "'.$original_path.'" "'.public_path('/').'" "'.$this->model_path.'" "'.$this->episode_len.'"');
        
        // ("Usage: python process_image.py <input_path> <output_path> <model_path> <episode_len>")
        
        $result = Process::run($command, function (string $type, string $output) {
            echo $output;
        });
        $result->successful();

        // Save preprocessed image to public disk
        $savePreprocessedImage->saveImage($this->id, $result->output());
    }
}
