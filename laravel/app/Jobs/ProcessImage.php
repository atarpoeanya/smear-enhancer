<?php

namespace App\Jobs;

use App\Models\Image;
use App\Models\ProcessedImage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;

class ProcessImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Image $original, public string $image_path, public string $output_folder, public string $model_path, public int $episode_len) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $imageName = 'st-'.time().'.png';

        $command = escapeshellcmd('python '.'"'.base_path('python-script/blood-enhancer/test_b.py').'"'
        .' "'.$this->image_path.'" "'
        .$this->output_folder.$imageName.'" "'
        .$this->model_path.'" "'
        .$this->episode_len.'"');

        // ("Usage: python process_image.py <input_path> <output_path> <model_path> <episode_len>")

        // $result = Process::run($command);

        // Save preprocessed image to model
        // $output_path = $result->output();
        // echo $result->errorOutput();
        // echo $result->output();
        $output_path = 'Yeah';

        $p_image = new ProcessedImage(['path' => $output_path]);

        $this->original->preprocessedImages()->save($p_image);

        // $result->successful();

    }
}
