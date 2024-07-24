<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;


class EvaluateImages
{
  function getPSNR(string $orginal_path, string $output_path) : float {
    // $psnr = [];
    // $original_image = Image::find($original_id);

    $command = escapeshellcmd('python '.'"'.base_path('python-script/blood-enhancer/psnr_eval.py') . '" "' . $orginal_path . '" "'. $output_path.'"');

    $result = Process::path(base_path())
            ->env([
                'SYSTEMROOT' => getenv('SYSTEMROOT'),
                'PATH' => getenv("PATH"),
            ])
            ->run($command);

    // $result = Process::run($command);
    $output = floatval($result->output());

    return $output;
  }
}