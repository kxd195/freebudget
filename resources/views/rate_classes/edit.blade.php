@extends('layouts.app')

@section('content')
<div class="small">
	<a href="{{ route('home') }}"><strong>Home</strong></a>
	&gt; <a href="{{ route('settings') }}"><strong>System Settings</strong></a>
	&gt; <a href="{{ route('rate_classes.index') }}"><strong>Rate Classes</strong></a>
	&gt; {{ isset($entry->id) ? 'Modify an Existing' : 'Create a New' }} Entry
</div>
<div class="panel panel-primary">
    <div class="panel-heading"><h1 class="panel-title">{{ isset($entry->id) ? 'Modify an Existing' : 'Create a New' }} Entry</h1></div>
    <div class="panel-body">
	{{ Form::model($entry, [
		'route' => isset($entry->id) ? ['rate_classes.update', $entry->id] : 'rate_classes.store', 
		'class' => 'form-horizontal']) }}    

	<div class="form-group @if ($errors->has('category_id')) has-error @endif">
		{{ Form::labelRequired('category_id', 'Category:', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-9">
		{{ Form::select('category_id', $categories, $value = null, ['class' => 'form-control', 'autofocus' => 'autofocus', 'placeholder' => 'Select...']) }}
		@if ($errors->has('category_id')) <p class="help-block">{{ $errors->first('category_id') }}</p> @endif
		</div>
	</div>

	<div class="form-group @if ($errors->has('name')) has-error @endif">
		{{ Form::labelRequired('name', 'Name:', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-9">
		{{ Form::text('name', $value = null, ['class' => 'form-control']) }}
		@if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
		</div>
	</div>

	<div class="form-group @if ($errors->has('code')) has-error @endif">
		{{ Form::labelRequired('code', 'Code:', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-2">
		{{ Form::text('code', $value = null, ['class' => 'form-control', 'maxlength' => 4]) }}
		</div>
		@if ($errors->has('code')) <p class="help-block">{{ $errors->first('code') }}</p> @endif
	</div>

	<div class="form-group @if ($errors->has('min_hours')) has-error @endif">
		{{ Form::label('min_hours', 'Min. Hours:', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-2">
		{{ Form::number('min_hours', $value = null, ['class' => 'form-control', 'step' => 'any']) }}
		</div>
		@if ($errors->has('min_hours')) <p class="help-block">{{ $errors->first('min_hours') }}</p> @endif
	</div>

    	<div class="form-group @if ($errors->has('rate')) has-error @endif">
    		{{ Form::label('rate', 'Rate:', ['class' => 'col-md-3 control-label']) }}
    		<div class="col-md-3">
    		<div class="input-group">
        		<span class="input-group-addon">$</span>
        		{{ Form::number('rate', $value = null, ['class' => 'form-control', 'step' => 'any']) }}
    		</div>
    		</div>
        	@if ($errors->has('rate')) <p class="help-block">{{ $errors->first('rate') }}</p> @endif
    	</div>

	<div class="form-group @if ($errors->has('is_addon')) has-error @endif">
		{{ Form::label('is_addon', 'For Addons Only?', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-9">
		<div class="radio">
			<label>
			{{ Form::radio('is_addon', '0', !$entry->is_addon ? true : false) }}
			No
			</label>
		</div>
		<div class="radio">
			<label>
			{{ Form::radio('is_addon', '1', $entry->is_addon ? true : false) }}
			Yes
			</label>
		</div>
		@if ($errors->has('is_addon')) <p class="help-block">{{ $errors->first('is_addon') }}</p> @endif
		</div>
	</div>

	<div class="form-group @if ($errors->has('bgcolor')) has-error @endif">
		{{ Form::labelRequired('bgcolor', 'Color:', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-9">
		{{ Form::select('bgcolor', $bgcolors, $value = null, ['class' => 'form-control']) }}
		@if ($errors->has('bgcolor')) <p class="help-block">{{ $errors->first('bgcolor') }}</p> @endif
		</div>
	</div>

	<div class="form-group">
	<div class="col-md-offset-3 col-md-9">
	{{ Form::submit('Save Changes', ['class' => 'btn btn-primary']) }}
	</div>
	</div>

	@if (isset($entry->id)) {{ method_field('PUT') }} @endif
	{{ Form::close() }}
	
	@component('components.delete', ['entry' => $entry, 'controller' => 'rate_classes'])
	@endcomponent
    </div>
</div>
@endsection