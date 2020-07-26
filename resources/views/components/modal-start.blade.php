<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id . '-label' }}" aria-hidden="true">
    <div class="modal-dialog {{ $modal_size ?? '' }}" role="document">
        <div class="modal-content">
            @if(isset($form))
                @if(isset($form['enctype']) && $form['enctype'])
                    <form action="{{ $form['action'] }}" id="{{ (isset($form['id']) && $form['id']) ? $form['id'] : '' }}" method="post" enctype="multipart/form-data">
                @else
                    <form action="{{ $form['action'] }}" id="{{ (isset($form['id']) && $form['id']) ? $form['id'] : '' }}" method="post">
                @endif
                @csrf
                @if(isset($method))
                    {{ method_field($method) }}
                @endif
            @endif
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id . '-label' }}">{{ $title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
