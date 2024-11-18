@extends('base')

@section('content')
<div class="w-full max-w-xs mx-auto py-3">
  <form action="{{ route('image.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 my-4">
    @csrf
    <div class="form-group">
      <img id="imagePreview" src="" alt="Image Preview" style="max-width: 100%; display: none;">
    </div>
    <div class="mb-4">
      <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Image:</label>
      <input type="file" name="image" id="image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" onchange="previewImage(event)">
    </div>
    <div class="mb-4">
      <label for="isRaw" class="block text-gray-700 text-sm font-bold mb-2">Reduce Image Quality</label>
      <input type="text" name="checkbox_value" value="1" id="checkbox_value">
      <input type="checkbox" id="isRaw" class="form-checkbox h-5 w-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500" name="isRaw">
    </div>
    <div class="mb-4">
      <label for="episode" class="block text-gray-700 text-sm font-bold mb-2">Episode:</label>
      <input type="number" name="episode" id="episode" min="1" max="15" value="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
    </div>
    <div class="mb-4">
      <label for="model" class="block text-gray-700 text-sm font-bold mb-2">Model:</label>
      <select name="model" id="model" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        <option value="1">Model 2000</option>
        <option value="2">Model 2500</option>
        <option value="3">Model 16 1000</option>
      </select>
    </div>
    <div class="flex items-center justify-between">
      <button type="submit" class="bg-orange-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Upload</button>
    </div>
  </form>
</div>
<div class="mt-10">
  <h2 class="text-center text-2xl font-bold bg-white">Uploaded Images</h2>
  <div>
    <div class="w-full flex justify-center items-center">
      <div style="width: 800px;"><canvas id="psnr"></canvas></div>
    </div>
  </div>
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-5 p-3">
    <input type="hidden" name="exclude" value="" id="exclude" value="">
    @foreach ($images as $image)
    <div class="border rounded p-2 bg-white" id='card-{{$image->id}}'>
      <img src="{{ asset('storage' .'/images/original/' . $image->path) }}" alt="Image" class="w-32 h-32 object-cover">

      <div class="mt-2">
        <input type="checkbox" id="isRaw" class="form-checkbox h-5 w-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500" name="isRaw" onclick="appendToArray(this, '{{$image->id}}')">
        <p><strong>Episode:</strong> {{ $image->episode }}</p>
        <p><strong>Model:</strong> {{ "model" }}</p>
        <p><strong>Uploaded:</strong> {{ $image->created_at->format('Y-m-d H:i:s') }}</p>
      </div>
      <div class="flex justify-between mt-2">
        <a href="{{ route('image.show', $image->id) }}" target="_self" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">Show</a>
        <form action="{{ route('image.destroy', $image->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
          @csrf
          @method('DELETE')
          <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">Delete</button>
        </form>
      </div>
    </div>
    @endforeach
  </div>
</div>
<!-- </div> -->
@endsection


@push('script')
<script>
  let chart;
  var psnrValues = @json($psnr);
  var entropyValues = @json($entropy);

  function appendToArray(element, image_id) {
    var exclude = document.getElementById('exclude') //1,2,3
    var excluded = []

    if (exclude.value !== '') {
      exclude.value.split(',').forEach(id => {
        excluded.push(id)
      });
    }


    if (excluded) {
      excluded.push(image_id)
      exclude.value = excluded
      parent = document.getElementById('card-'.concat(image_id))
      parent.style.display = "none"
      // console.log(excluded)
      updateChart(excluded)

    }
  }

  const data = {
    labels: psnrValues.map((_, index) => index + 1),
    datasets: [{
      label: 'AVERAGE PSNR',
      data: psnrValues,
      fill: false,
      borderColor: 'rgb(75, 192, 192)',
      tension: 0
    },
    {
      label: 'AVERAGE Entropy',
      data: entropyValues,
      fill: false,
      borderColor: 'rgb(255, 0, 0)',
      tension: 0
    }
  ]
  };
  
  document.addEventListener('DOMContentLoaded', function() {
  chart = new Chart(
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

  function updateChart(psnr) {
    const params = JSON.stringify(psnr);
    console.info(params)
    fetch(`/updateChart?data=${params}`).
    then(response => response.json()).
    then(data => {
      chart.data.datasets[0].data = data[0]
      chart.data.datasets[1].data = data[1]
      chart.update()

    }).catch(error => console.error('Error fetching data:', error))


  }


  function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function() {
      var output = document.getElementById('imagePreview');
      output.src = reader.result;
      output.style.display = 'block';
    };
    reader.readAsDataURL(event.target.files[0]);
  }

  // function updateHiddenInput(checkbox) {
  //   // Get the hidden input field
  //   var hiddenInput = document.querySelector('input[name="checkbox_value"]');

  //   // Update the value based on checkbox state
  //   hiddenInput.value = checkbox.checked ? 1 : 0;
  // }


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

  // var psnrValues = psnrData.map(function(item) {
  //     return item.psnr;
  // });
</script>
@endpush