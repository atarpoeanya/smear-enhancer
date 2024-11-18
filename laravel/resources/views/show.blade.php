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
    <form action="{{route('metric.update')}}" method="post">
        @csrf
        <input type="hidden" name="id" id="id" value="{{$image_id}}">
        <button type="submit">Update Metric</button>
    </form>
    <form action="{{route('image-original.download', $image_id)}}" method="GET">
        <button type="submit">DOWNLOAD</button>
    </form>
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
            <img src="{{ asset('storage'. '/images/preprocessed/'.$p_image->path) }}" alt="Image Step {{$loop->index + 1}}" class="thumbnail w-full h-full object-cover max-w-52 max-h-52" data-full="{{ asset('storage'. '/images/preprocessed/'.$p_image->path) }}">

            <img src="{{ asset('storage'. '/images/action/'.$p_image->colormap_path) }}" alt="Image" class="w-full h-full object-cover max-w-52 max-h-52 hidden">
        </div>
        <div class="p-4 text-wrap">
            <h3 class="text-lg text-center  font-semibold">Step {{$loop->index + 1}}</h3>
            <!-- <span class="">{{ $p_image->path }}</span> -->
            <div class="">
                <span class="font-bold">PSNR: {{ $p_image->psnr }}</span>
                <span class="font-bold">MSE: {{ $p_image->mse }}</span>
                <span class="font-bold">Entropy: {{ $p_image->entropy }}</span>
            </div>
            <form action="{{route('image.download', $p_image->id)}}" method="GET">
                <button type="submit">DOWNLOAD</button>
            </form>
        </div>
    </div>
    @endforeach
    <div id="myModal" class="modal fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50">
        <span class="close text-white text-4xl absolute top-5 right-10 cursor-pointer">&times;</span>
        <div class="modal-content max-w-3xl mx-auto">
            <img id="img01" class="w-full rounded shadow-lg">
            <div id="caption" class="text-center text-gray-300 mt-4"></div>
        </div>
    </div>
</div>
@endsection

@push('script')

<script>
    // import Chart from 'chart.js/auto';
    // JavaScript to handle image toggle


    document.addEventListener('DOMContentLoaded', function() {
        // Get the modal

        var modal = document.getElementById("myModal");

        // Get the image and insert it inside the modal - use its "alt" text as a caption
        var modalImg = document.getElementById("img01");
        var captionText = document.getElementById("caption");
        const images = document.querySelectorAll('.toggle-images img');

        images.forEach(image => {
            image.addEventListener('click', function() {
                // Toggle active class on the images
                images.forEach(img => img.classList.toggle('hidden'));
            });
        });




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

        document.querySelectorAll('.thumbnail').forEach(function(img) {
            img.onclick = function() {
                modal.style.display = "flex";
                modalImg.src = this.dataset.full;
                captionText.innerHTML = this.alt;
            }

            modalImg.onclick = function() {
                modal.style.display = "none";
            }
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



    // Get the <span> element that closes the modal


    // When the user clicks on <span> (x), close the modal
</script>
@endpush