<?php

namespace App\Http\Controllers;

use App\Events\ImageProcessed;
use App\Models\Image;
use App\Models\ProcessedImage;
use App\Services\CreatePreprocessingJob;
use App\Services\EvaluateImages;
use Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;

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
            'checkbox_value' => 'required|int',
        ]);

        $path = $request->file('image')->store('', 'original');
        // $path = Storage::disk('original')->putFile('', $request->file('image'));
        $episode = $request->input('episode');
        $model = $request->input('model');
        $is_raw = $request->input('checkbox_value') ? true : false;
        Debugbar::info($is_raw);

        $image = new Image();
        $image->path = $path;
        $image->episode = $episode;
        $image->save();

        $parameter = json_encode([
            'path' => $image->path,
            'ep' => $episode,
            'model' => $model,
            'isRaw' => $is_raw
        ]);


        // Dispatch job to background
        return redirect()->route('loading', ['id' => $image->id, 'data' => urlencode($parameter)]);
    }

    public function loading($id, $data)
    {
        
        $parameter = json_decode(urldecode($data), true);
        $model = $this->temp_model[$parameter['model'] - 1];

        $service = new CreatePreprocessingJob;
        $service->createJob($id, $parameter['path'], $model, $parameter['ep'], $parameter['isRaw']);

        $episode_len = $parameter['ep'];

        return view('loading')
        ->with(['current_episode' => 0,
                'episode_len'=> $episode_len, 
                'imageId' => $id]);
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
        $psnr = ProcessedImage::where('images_id', $image_id)->oldest()->get('psnr')->toArray();

        // Log::info(min($psnr));
        // Log::info(max($psnr));
        // $parameter = json_encode($psnr);

        return view('show')->with(['original' => $original, 'processed_images' => $processed_images, 'psnr' => $psnr]);
    }

    public function thisIsATest(Request $request) {

        // $service = new EvaluateImages;
        // $result = $service->getPSNR("We are testing");
        // Debugbar::info($result);
        // $image = Image::find();
        // $p = $image->processedImages()->get();

        // $parameter = json_decode($paramete, true);
        // return view('testing')->with('result', $p);
        return view('loading',['imageId'=> 1, 'max_episode' => 5, 'path'=> 'random.png']);
    }

    function pingingTest(Request $request) {

        broadcast(new ImageProcessed($request->id, $request->path, $request->episode));
        return back();
    }
}
