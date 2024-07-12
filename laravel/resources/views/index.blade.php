<!DOCTYPE html>
<html>
<head>
    <title>Laravel Image Upload</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h3 class="text-center">Blood Smear Enhancer</h3>
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <strong>{{ $message }}</strong>
                    </div>
                    <img src="storage/images/original/{{ Session::get('image') }}" width="300" />
                @endif
                <form method="POST" action="{{ route('image.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="image">Choose Image</label>
                        <input type="file" class="form-control-file" id="image" name="image" onchange="previewImage(event)">
                    </div>
                    <div class="form-group">
                        <img id="imagePreview" src="" alt="Image Preview" style="max-width: 100%; display: none;">
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
              <div class="container mt-5">
                <h4>List of image</h4>
                {{count($images)}}
              @foreach ($images as $image)
              <div class="container flex justify-between">
                  <p>{{ $image->path }}</p>
                  <form action="{{ route('image.delete', $image->id)}}" method="post">
                    @csrf
                    @method('delete')
                    <button type="submit">Delete button</button>
                  </form>
              </div>
              @endforeach
              </div>
            </div>
        </div>
    </div>

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
    </script>
</body>
</html>
