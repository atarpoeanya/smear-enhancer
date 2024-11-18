<?php

namespace App\Jobs;

use App\Events\ImageProcessed;
use App\Services\SavePreprocessedImage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Process\ProcessResult;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class ProcessImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $original_id, public string $original_path, public string $model_path, public int $episode_len, public int $max_episode , public bool $hasRaw = False, public float $exposure_factor = 1, public bool $first = true) {}

    /**
     * Execute the job.
     */
    public function handle(SavePreprocessedImage $savePreprocessedImage): void
    {

        $original_path = Storage::disk('original')->path($this->original_path);
        if (!$this->first) {
            $original_path = Storage::disk('preprocessed')->path($this->original_path);
        }

        $temp_name = 'st-'.str_replace('.', '', microtime(true)).'.png';
        $cm_name = 'cm-'.str_replace('.', '', microtime(true)).'.png';
        $output_path = public_path('storage').'/'.$temp_name;
        
        $colormap_path = public_path('storage') . '/' . $cm_name;
        
        $result = $this->runCommand($original_path, $output_path, $colormap_path, $this->hasRaw, $this->exposure_factor);

        $this->episode_len--;
        if ($result->successful()) {
            // Save preprocessed image to public disk
            $previous_image = $savePreprocessedImage->saveImage($this->original_id, $temp_name, $cm_name);

            // $fe_path = public_path('storage') . '/images/preprocessed/' . $previous_image[1];
            // Broadcast::on('image.'.$this->original_id)->as('ImageProcessed')->with(['path'=> $fe_path])->send();
            broadcast(new ImageProcessed($this->original_id, $previous_image[1], $this->max_episode - $this->episode_len));
            // $event->dispatch();

            if ($this->episode_len != 0) {
                $this->appendToChain(new ProcessImage($this->original_id, $previous_image[1], $this->model_path, $this->episode_len, $this->max_episode , false, 1 ,false));
            }
        }

        Log::error($result->errorOutput());
        
    }

    private function runCommand($original_path, $output_path, $colormap_path, $hasRaw = False, $exposure_factor = 1) : ProcessResult {

        $command = escapeshellcmd('python '.'"'.base_path('python-script/blood-enhancer/test_b.py').'" "'.$original_path.'" "'.$output_path.'" "'.$this->model_path.'" "'. $colormap_path .'" "' . public_path('storage') . '" "'. $hasRaw .'" "'. $exposure_factor.'"');

        return Process::run($command);
    }
}
