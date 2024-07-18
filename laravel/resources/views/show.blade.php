
@extends('base')

@section('title', 'Image Upload Dashboard')

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden m-2">
            <div class="w-full flex justify-center items-center">
                <img src="{{ asset('storage'. '/images/original/' .$original->path) }}" alt="Image" class="w-full h-full object-cover max-w-52 max-h-52">
            </div>
            <div class="p-4">
                <h3 class="text-lg text-center font-semibold">ORIGINAL</h3>
            </div>
        </div>        
        @foreach($processed_images as $p_image)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden m-2">
                <div class="w-full flex justify-center items-center">
                    <img src="{{ asset('storage'. '/images/preprocessed/'.$p_image->path) }}" alt="Image" class="w-full h-full object-cover max-w-52 max-h-52">
                </div>
                <div class="p-4">
                    <h3 class="text-lg text-center font-semibold">Step {{$loop->index + 1}}</h3>
                </div>
            </div>
        @endforeach
    </div>
@endsection
