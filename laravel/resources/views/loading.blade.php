@extends('base')

@section('content')

<div class="text-center">
  <div class="flex items-center justify-center mb-4" id="spinner">
    <div class="w-24 h-24 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
  </div>
  <div class="mb-4">
    <img id="imagePreview" src="{{asset('storage'. '/images/preprocessed/' . '')}}" alt="Image Preview" class="w-48 h-48 object-cover mx-auto rounded hidden">
  </div>
  <div class="text-lg font-semibold mb-2">
    <span id="currentStep"> {{ $current_episode}}</span> / <span id="maxSteps">{{ $episode_len}}</span> Steps
  </div>
  <a id="proceedButton" href="{{ route('image.show', $imageId) }}" target="_self" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded hidden">See result</a>

</div>


@endsection

@push('script')
<script type="module">
  var id = @json($imageId);
  var maxEpisode = @json($episode_len);
  document.addEventListener('DOMContentLoaded', function() {
    Echo.channel(`image.${id}`)
      .listen('ImageProcessed', (event) => {
        // console.log(event.path);
        var output = document.getElementById('imagePreview');
        var current_episode = document.getElementById('currentStep');
        var proceedButton = document.getElementById('proceedButton');
        var spinner = document.getElementById('spinner');
        output.src = "{{ asset('storage'. '/images/preprocessed/') }}/" + event.path;
        output.style.display = 'block';

        current_episode.textContent = event.current_episode;

        if (event.current_episode == maxEpisode) {
          proceedButton.style.display = 'block'
          spinner.style.display = 'hidden'
        }
      });
  });
</script>
@endpush