<?php

namespace App\Services;

use App\Jobs\ProcessImage;
use App\Models\Image;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Process;

class EvaluateImages
{
  function getPSNR($var = "no") : string {
    // $psnr = [];
    // $original_image = Image::find($original_id);

    $command = escapeshellcmd('python '.'"'.base_path('python-script/blood-enhancer/psnr_eval.py') . '" "' . $var . '"');

    $result = Process::run($command);

    $output = $result->errorOutput();

    return $output;
  }
}