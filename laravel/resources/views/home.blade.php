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
        <input type="hidden" name="checkbox_value" value="0" id="checkbox_value">
        <input type="checkbox" id="isRaw" class="form-checkbox h-5 w-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500" name="isRaw" onclick="updateHiddenInput(this)">
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
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-5 p-3">
    @foreach ($images as $image)
    <div class="border rounded p-2 bg-white">
      <img src="{{ asset('storage' .'/images/original/' . $image->path) }}" alt="Image" class="w-32 h-32 object-cover">
      <div class="mt-2">
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
      function previewImage(event) {
          var reader = new FileReader();
          reader.onload = function(){
              var output = document.getElementById('imagePreview');
              output.src = reader.result;
              output.style.display = 'block';
          };
          reader.readAsDataURL(event.target.files[0]);
      }

      function updateHiddenInput(checkbox) {
            // Get the hidden input field
            var hiddenInput = document.querySelector('input[name="checkbox_value"]');
            
            // Update the value based on checkbox state
            hiddenInput.value = checkbox.checked ? 1 : 0;
        }
  </script>
@endpush