@extends('layouts.app')

@section('content')
<div>
	<a href="{{ route('home') }}"><strong>Home</strong></a>
	&gt; <a href="{{ route('settings') }}"><strong>System Settings</strong></a>
	&gt; <a href="{{ route('units.index') }}"><strong>Units</strong></a>
	&gt; {{ isset($entry->id) ? 'Modify an Existing' : 'Create a New' }} Entry
</div>
<div class="panel panel-primary">
    <div class="panel-heading"><h1 class="panel-title">{{ isset($entry->id) ? 'Modify an Existing' : 'Create a New' }} Entry</h1></div>
    <div class="panel-body">
	{{ Form::model($entry, [
		'route' => isset($entry->id) ? ['units.update', $entry->id] : 'units.store', 
		'class' => 'form-horizontal']) }}    

	<div class="form-group @if ($errors->has('name')) has-error @endif">
		{{ Form::labelRequired('name', 'Name:', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-9">
		{{ Form::text('name', $value = null, ['class' => 'form-control', 'autofocus' => 'autofocus']) }}
		@if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
		</div>
	</div>

	<div class="form-group">
	<div class="col-md-offset-3 col-md-9">
	{{ Form::submit('Save Changes', ['class' => 'btn btn-primary']) }}
	</div>
	</div>

	@if (isset($entry->id)) {{ method_field('PUT') }} @endif
	{{ Form::close() }}
	
	@component('components.delete', ['entry' => $entry, 'controller' => 'units'])
	@endcomponent
    </div>
</div>
@endsection