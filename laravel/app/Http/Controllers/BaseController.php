<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessImage;
use App\Models\Image;
use Barryvdh\Debugbar\Facade as Debugbar;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    private $original_path = 'storage/images/original/';

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

        $imageName = time().'.'.$request->image->extension();

        $request->image->move(public_path($this->original_path), $imageName);

        $image = new Image();
        $image->path = $this->original_path.$imageName;
        $image->save();

        return back();
    }

    // Save the image path to the database
    // DebugBar::info([ $image->path, $this->preprocessed_path, base_path($this->temp_model)]);
    // ProcessImage::dispatch($image, public_path($image->path), public_path($this->preprocessed_path), base_path($this->temp_model), 1);
    public function deleteImage($id)
    {
        $image = Image::find($id);
        $image->delete();

        return back()
            ->with('success', 'Post deleted successfully');
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
