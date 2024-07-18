<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\ProcessedImage;
use App\Services\CreatePreprocessingJob;
use App\Services\EvaluateImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BaseController extends Controller
{
    private $original_path = 'storage/images/original';

    private $preprocessed_path = 'storage/images/preprocessed/';

    private $temp_model = ['python-script/blood-enhancer/model/17_2000_model.npz',
                            'python-script/blood-enhancer/model/17_2500_model.npz',
                            'python-script/blood-enhancer/model/16_1000_model.npz'];
    // private $temp_model = 'python-script/blood-enhancer/model/model_6000.npz';

    public function index()
    {

        return view('home')->with('images', Image::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'episode' => 'required|int',
            'model' => 'required|int',
        ]);

        $path = $request->file('image')->store('', 'original');
        // $path = Storage::disk('original')->putFile('', $request->file('image'));
        $episode = $request->input('episode');
        $model = $request->input('model');

        $image = new Image();
        $image->path = $path;
        $image->save();

        $parameter = json_encode([
            'path' => $image->path,
            'ep' => $episode,
            'model' => $model
        ]);

        // Dispatch job to background
        return redirect()->route('loading', ['id' => $image->id, 'data' => urlencode($parameter)]);
    }

    public function loading($id, $data)
    {
        /**
         * public Image $original,
         * public string $image_path,
         * public string $output_folder,
         * public string $model_path,
         * public int $episode_len
         * **/
        $parameter = json_decode(urldecode($data), true);
        $model = $this->temp_model[$parameter['model'] - 1];

        $service = new CreatePreprocessingJob;
        $service->createJob($id, $parameter['path'], $model, $parameter['ep']);

        $episode_len = $parameter['ep'];

        return view('loading')
        ->with(['current_episode' => 0,
                'episode_len'=> $episode_len, 
                'id' => $id]);
    }

    public function deleteImage($id)
    {
        $image = Image::find($id);
        $image->delete();

        Storage::disk('original')->delete($image->path);

        return back();
    }

    public function show(string $image_id)
    {
        $original = Image::find($image_id);
        $processed_images = ProcessedImage::where('images_id', $image_id)->oldest()->get();

        return view('show')->with(['original' => $original, 'processed_images' => $processed_images]);
    }

    public function thisIsATest() {
        $service = new EvaluateImages;
        $result = $service->getPSNR("We are testing");

        return view('testing')->with('result', $result);
    }
}
