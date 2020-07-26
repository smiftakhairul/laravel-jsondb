</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    @if(isset($form) && $form)
        @if(isset($id))
            <button type="submit" class="btn btn-primary {{ isset($class) ? $class : '' }}" id="{{ $id }}">
        @else
            <button type="submit" class="btn btn-primary {{ isset($class) ? $class : '' }}">
        @endif
        {!! (isset($title) && $title) ? $title : 'Save' !!}
        </button>
    @endif
</div>
@if(isset($form) && $form)
    </form>
@endif
</div>
</div>
</div>
