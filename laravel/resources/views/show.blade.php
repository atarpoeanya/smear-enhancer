@extends('base')

@section('title', 'Image Upload Dashboard')

@section('content')
<div class="bg-white rounded-lg shadow-lg overflow-hidden m-2">
    <div class="w-full flex justify-center items-center pt-2">
        <img src="{{ asset('storage'. '/images/original/' .$original->path) }}" alt="Image" class="w-full h-full object-cover max-w-52 max-h-52">
    </div>
    <div class="p-4">
        <h3 class="text-lg text-center font-semibold">ORIGINAL</h3>
    </div>
</div>
<div class="bg-white rounded-lg shadow-lg overflow-hidden m-2">
    <!-- <div style="width: 500px;"><canvas id="dimensions"></canvas></div><br/> -->
    <div class="w-full flex justify-center items-center">
        <div style="width: 800px;"><canvas id="psnr"></canvas></div>
    </div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    @foreach($processed_images as $p_image)
    <div class="bg-white rounded-lg shadow-lg overflow-hidden m-2">
        <div class="w-full flex justify-center items-center toggle-images pt-2">
            <img src="{{ asset('storage'. '/images/preprocessed/'.$p_image->path) }}" alt="Image" class="w-full h-full object-cover max-w-52 max-h-52">

            <img src="{{ asset('storage'. '/images/action/'.$p_image->colormap_path) }}" alt="Image" class="w-full h-full object-cover max-w-52 max-h-52 hidden">
        </div>
        <div class="p-4 text-wrap">
            <h3 class="text-lg text-center  font-semibold">Step {{$loop->index + 1}}</h3>
            <!-- <span class="">{{ $p_image->path }}</span> -->
            <span class="font-bold">PSNR: {{ $p_image->psnr }}</span>
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('script')

<script>
    // import Chart from 'chart.js/auto';
    // JavaScript to handle image toggle
    document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('.toggle-images img');

        images.forEach(image => {
            image.addEventListener('click', function() {
                // Toggle active class on the images
                images.forEach(img => img.classList.toggle('hidden'));
            });
        });
    });
    var psnrData = @json($psnr);
    var psnrValues = psnrData.map(function(item) {
        return item.psnr;
    });

    const data = {
        labels: psnrValues.map((_, index) => index + 1),
        datasets: [{
            label: 'PSNR',
            data: psnrValues,
            fill: false,
            borderColor: 'rgb(75, 192, 192)',
            tension: 0
        }]
    };
    document.addEventListener('DOMContentLoaded', function() {


        var chart = new Chart(
            document.getElementById('psnr'), {
                type: 'line',
                data: data,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }

            }
        )
    });
</script>
@endpush