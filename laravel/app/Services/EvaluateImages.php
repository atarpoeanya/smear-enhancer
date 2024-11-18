<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class EvaluateImages
{
  public function getPSNR(string $orginal_path, string $output_path): float
  {
    // $psnr = [];
    // $original_image = Image::find($original_id);

    $command = escapeshellcmd('python ' . '"' . base_path('python-script/blood-enhancer/psnr_eval.py') . '" "' . $orginal_path . '" "' . $output_path . '"');

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

  public function getMSE(string $orginal_path, string $output_path): float
  {
    $command = escapeshellcmd('python ' . '"' . base_path('python-script/blood-enhancer/mse_eval.py') . '" "' . $orginal_path . '" "' . $output_path . '"');

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

  public function getEntropy(string $output_path): float
  {
    
    $command = escapeshellcmd('python ' . '"' . base_path('python-script/blood-enhancer/entropy_eval.py') . '" "' . $output_path . '"');
    

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

  function calculateAverage($evalated_column = "psnr", $minRelatedCount = 15, $ids = []): array
  {

    $filteredImages = Image::whereHas('processedImages', function ($query) use ($minRelatedCount) {
      $query->select('images_id') // Make sure to select the column you want to group by
        ->groupBy('images_id')
        ->havingRaw('COUNT(*) <= ?', [$minRelatedCount]);
    })
      ->pluck('id');


    $ranked_processed_images = DB::table('processed_images')
    ->limit(10)
      ->select(
        'id',
        'images_id',
        $evalated_column,
        'created_at',
        DB::raw('ROW_NUMBER() OVER (PARTITION BY images_id ORDER BY created_at) as row_num')
      )
      ->toSql();

    $average_psnr = DB::table(DB::raw("($ranked_processed_images) as ranked"))
      ->select('row_num', DB::raw("AVG($evalated_column) as average_psnr"))
      ->whereIn('images_id', $filteredImages)
      ->whereNotIn('images_id', $ids)
      ->groupBy('row_num')
      ->orderBy('row_num')
      ->pluck('average_psnr')->toArray();

    return $average_psnr;
  }
}
