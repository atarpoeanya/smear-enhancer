<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessImage;
use App\Models\Image;
use App\Services\CreatePreprocessingJob;
use Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class BaseController extends Controller
{
    private $original_path = 'storage/images/original';

    private $preprocessed_path = 'storage/images/preprocessed/';

    private $temp_model = 'python-script/blood-enhancer/model/17_2500_model.npz';

    public function index()
    {

        return view('index')->with('images', Image::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = $request->file('image')->store('', 'original');
        $episode_len = 1;

        $image = new Image();
        $image->path = $path;
        $image->save();

        $parameter = json_encode([
            'path' => $image->path,
            'ep' => $episode_len
        ]);
        

        // Dispatch job to background

        return redirect()->route('loading', ['id' => $image->id, 'data'=>urlencode($parameter)]);
    }


    public function loading($id, $data) {
        /**
         * public Image $original, 
         * public string $image_path, 
         * public string $output_folder, 
         * public string $model_path, 
         * public int $episode_len
         * **/ 
        $parameter = json_decode(urldecode($data), true);

        $service = new CreatePreprocessingJob;
        $service->createJob($id, $parameter['path'], $this->temp_model, $parameter['ep']);
        $episode_len = $parameter['ep'];

        return view('loading')->with('current_episode', 0)->with('episode_len', $episode_len);
    }


    public function deleteImage($id)
    {
        $image = Image::find($id);
        $image->delete();

        Storage::disk('original')->delete($image->path);

        return back();
    }

    public function store_p(Request $request)
    {

        $originalFolderPath = 'storage/images/original/';
        $preprocessedFolderPath = 'storage/images/original/';

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imageName = time().'.'.$request->image->extension();
        $originalImagePath = public_path($originalFolderPath.$imageName);

        // Save the original image
        $request->image->move(public_path($originalFolderPath), $imageName);

        // Call the Python script for preprocessing
        $preprocessedImagePath = public_path($preprocessedFolderPath.$imageName);
        $command = escapeshellcmd('python '.base_path('process_image.py').' '.$originalImagePath.' '.$preprocessedImagePath);
        $output = shell_exec($command);

        // Save the image path to the database
        $image = new Image();
        $image->path = $preprocessedFolderPath.$imageName;
        $image->save();

        return back()->with('success', 'You have successfully uploaded and processed the image.')
            ->with('image', $image->path);
    }
}
