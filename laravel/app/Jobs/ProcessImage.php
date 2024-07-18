<?php

namespace App\Jobs;

use App\Services\SavePreprocessedImage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class ProcessImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $original_id, public string $original_path, public string $model_path, public int $episode_len, public bool $first = true) {}

    /**
     * Execute the job.
     */
    public function handle(SavePreprocessedImage $savePreprocessedImage): void
    {

        $original_path = Storage::disk('original')->path($this->original_path);
        if (!$this->first) {
            $original_path = Storage::disk('preprocessed')->path($this->original_path);
            // dump($original_path);
        }
        // dump($original_path ? $original_path : "It's null");

        $temp_name = 'st-'.str_replace('.', '', microtime(true)).'.png';
        $output_path = public_path('storage').'/'.$temp_name;
        // dump();

        $command = escapeshellcmd('python '.'"'.base_path('python-script/blood-enhancer/test_b.py').'" "'.$original_path.'" "'.$output_path.'" "'.$this->model_path.'" "'. 1 .'"');

        // // ("Usage: python process_image.py <input_path> <output_path> <model_path> <episode_len>")
        // dump($command);
        $result = Process::run($command);
        // dump($this->episode_len);
        $this->episode_len--;
        if ($result->successful()) {
            // Save preprocessed image to public disk
            $previous_image = $savePreprocessedImage->saveImage($this->original_id, $temp_name);

            if ($this->episode_len != 0) {
                $this->appendToChain(new ProcessImage($this->original_id, $previous_image[1], $this->model_path, $this->episode_len, false));
            }
        }
        Log::error($result->errorOutput());

    }
}
