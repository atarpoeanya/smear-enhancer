<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Grid</title>
    @vite('resources/css/app.css')
    
</head>
<body>
    <div class="flex flex-wrap overflow-x-auto">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden m-2">
                <img src="{{ asset('storage'. '/images/original/' .$original->path) }}" alt="Image" class="w-full h-full object-cover max-w-52 max-h-52">
                <div class="p-4">
                    <h3 class="text-lg text-center font-semibold">ORIGINAL</h3>
                </div>
            </div>        
            @foreach($processed_images as $p_image)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden m-2">
                    <img src="{{ asset('storage'. '/images/preprocessed/'.$p_image->path) }}" alt="Image" class="w-full h-full object-cover max-w-52 max-h-52">
                    <div class="p-4">
                        <h3 class="text-lg text-center font-semibold">Step {{$loop->index + 1}}</h3>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
