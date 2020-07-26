@extends('layout.app')

@section('content')
    <div class="container">
        <div class="row header">
            <div class="col-md-12"><h4>Image List</h4></div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#upload-image-modal">Upload Image</button>
            </div>
            <div class="col-md-9">
                <input type="text" name="search" class="form-control" id="image-search" placeholder="Search">
            </div>
        </div>
        <hr>

        <div class="row all-image-list">
            @if(!empty($images))
                @foreach($images as $index => $image)
                    <div class="col-md-3 single-item">
                        <div class="card">
                            <img src="{{ $image['image'] }}" class="card-img-top" alt="...">
                            <div class="card-body">
                                <p class="card-text">{{ $image['title'] }}</p>
                            </div>

                            <div class="card-footer">
                                <form action="{{ route('images.destroy', $image['id']) }}"
                                      onsubmit="event.preventDefault(); deleteImage($(this), '{{ $image['id'] }}')" method="post">
                                    @csrf
                                    {{ method_field('DELETE') }}
                                    <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection

@section('modal')
{{--    Image upload modal--}}
    @component('components.modal-start', [
        'id' => 'upload-image-modal',
        'title' => 'Upload Image',
        'form' => [
            'id' => 'upload-image-form',
            'action' => route('images.store'),
            'enctype' => true
        ],
    ]);
    @endcomponent
    <div class="modal-upload-section">
        <div class="alert-section"></div>
        <div class="form-progress" style="display: none">
            <p class="progress-text text-center">Please wait while the image is being uploaded...</p>
            <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>
        </div>
        <div class="form-section">
            <div class="form-group">
                <label for="title">Image Title:</label>
                <input type="text" class="form-control" id="title" name="title" minlength="5" placeholder="Image Title" required>
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-group">
                <input type="file" name="image" accept="image/png" id="imageLabel" required>
{{--                <label class="custom-file-label" for="imageLabel">Choose Image</label>--}}
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>
    @component('components.modal-end', [
        'title' => 'Upload',
        'form' => true
    ]);
    @endcomponent
@endsection

@push('js')
    <script>
        imageViewer();
        function imageViewer() {
            var $image = $('.card-img-top');
            $image.viewer();
        }

        $('#upload-image-modal').on('hidden.bs.modal', function () {
            $(this).find('.alert-section').html('');
        });

        $('#upload-image-form').validate({
            submitHandler: function (form) {
                $(form).ajaxSubmit({
                    beforeSend: function() {
                        $(form).find('.form-progress').fadeIn();
                        $(form).find('.form-section').hide();
                        $(form).find('.alert-section').hide();

                        let percentComplete = 0;
                        let progressWrap = $(form).find('.form-progress .progress-bar');
                        updateProgressBar(progressWrap, percentComplete);
                    },
                    uploadProgress:function(event, position, total, percentComplete)
                    {
                        let progressWrap = $(form).find('.form-progress .progress-bar');
                        updateProgressBar(progressWrap, percentComplete);
                    },
                    success: function (res) {
                        if (res.status === 'SUCCESS') {
                            let wrap = '<div class="col-md-3 single-item">\n' +
                                '                        <div class="card">\n' +
                                '                            <img src="'+ res.data.image +'" class="card-img-top" alt="...">\n' +
                                '                            <div class="card-body">\n' +
                                '                                <p class="card-text">'+ res.data.title +'</p>\n' +
                                '                            </div>\n' +
                                '<div class="card-footer">\n' +
                                '                                <form action="images/'+ res.data.id +'" onsubmit="event.preventDefault(); deleteImage($(this), '+ res.data.id +')" method="post">\n' +
                                '                                    @csrf \n' +
                                '                                    {{ method_field('DELETE') }} \n' +
                                '                                    <button type="submit" class="btn btn-danger btn-sm">Remove</button>\n' +
                                '                                </form>\n' +
                                '                            </div>' +
                                '                        </div>\n' +
                                '                    </div>';
                            // $('.all-image-list').prepend(wrap);
                            $(wrap).hide().prependTo('.all-image-list').fadeIn(500);
                        }
                        let type = (res.status === 'SUCCESS') ? 'success' : 'danger';
                        let message = (res.status === 'SUCCESS') ? res.message : 'Image could not be uploaded.';
                        $('.alert-section').html(customAlert(type, message));
                    },
                    error: function (err) {
                        popup('warning', null, 'Something went wrong. Please try again.');
                    },
                    complete: function () {
                        $(form)[0].reset();
                        $(form).find('.form-progress').hide();
                        $(form).find('.form-section').fadeIn();
                        $(form).find('.alert-section').fadeIn();

                        let percentComplete = 0;
                        let progressWrap = $(form).find('.form-progress .progress-bar');
                        updateProgressBar(progressWrap, percentComplete);
                        imageViewer();
                    }
                })
            }
        });

        $('#image-search').keyup(function() {
            var value = $(this).val().toLowerCase();
            $('.all-image-list .card-text').filter(function() {
                $(this).closest('.single-item').toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        function updateProgressBar(progressWrap, percentComplete) {
            let text = !percentComplete
                ? 'Please wait while the image is being uploaded...'
                : 'Image successfully uploaded.';
            progressWrap.text(percentComplete + '%');
            progressWrap.css('width', percentComplete + '%');
            progressWrap.attr('aria-valuenow', percentComplete);
            progressWrap.closest('.form-progress').find('.progress-text').text(text);
        }

        function deleteImage(_this, id) {
            let form = _this[0];
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Delete It'
            }).then((result) => {
                if (result.value) {
                    $(form).ajaxSubmit({
                        beforeSend: function () {

                        },
                        success: function (res) {
                            if (res.status === 'SUCCESS') {
                                Swal.fire('Deleted!', res.message, 'success');
                                $(form).closest('.single-item').fadeOut(1000);
                            } else {
                                Swal.fire('Deleted!', res.message, 'error');
                            }
                        },
                    });
                }
            })
        }
    </script>
@endpush
