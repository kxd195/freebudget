@if (isset($entry->id))
{{ Form::model($entry, [ 'route' => [ $controller . '.destroy', $entry->id]]) }}
    {{ method_field('DELETE') }}
    <div class="form-group">
        <div class="col-md-12">
        {{ Form::submit(isset($caption) ? $caption : 'Delete Entry', [
            	'class' => 'btn btn-danger pull-right',
            	'onclick' => 'return confirm("' . (isset($message) ? $message : 'Are you sure you would like to delete this entry?') . '");']) }}
        </div>
    </div>
{{ Form::close() }}
@endif