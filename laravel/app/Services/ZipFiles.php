<?php

namespace App\Services;


use ZipArchive;

class ZipFies
{
  public function makeZipWithFiles(string $zipPathAndName, array $filesAndPaths): string
  {
    $zip = new ZipArchive();
    $tempFile = tmpfile();
    $tempFileUri = stream_get_meta_data($tempFile)['uri'];

    if ($zip->open($tempFileUri, ZipArchive::CREATE) !== TRUE) {
      echo 'Could not open ZIP file.';
      return false;
    }

    // Add File in ZipArchive
    foreach ($filesAndPaths as $file) {
      if (!$zip->addFile($file, basename($file))) {
        echo 'Could not add file to ZIP: ' . $file;
      }
    }
    // Close ZipArchive
    $zip->close();

    echo 'Path:' . $zipPathAndName;

    if(rename($tempFileUri, $zipPathAndName)){
      return $zipPathAndName;
    }
  }
}
