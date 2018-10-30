@extends('layouts.app')

@section('content')
@component('components.messages')
@endcomponent

<div>
	<a href="{{ route('home') }}"><strong>Home</strong></a>
	&gt; <a href="{{ route('settings') }}"><strong>System Settings</strong></a>
	&gt; Modify Global Settings
</div>
<div class="panel panel-primary">
    <div class="panel-heading"><h1 class="panel-title">Modify Global Settings</h1></div>
    <div class="panel-body">
	{{ Form::model($entry, [
		'route' => isset($entry->id) ? ['global_settings.update', $entry->id] : 'global_settings.store', 
		'class' => 'form-horizontal']) }}    

	<h2>Overtime</h2>

	<div class="form-group col-md-6 @if ($errors->has('hours_overtime')) has-error @endif">
		{{ Form::labelRequired('hours_overtime', 'Hours After:', ['class' => 'col-md-6 control-label']) }}
		<div class="col-md-6">
		{{ Form::number('hours_overtime', $value = null, ['class' => 'form-control', 'autofocus' => 'autofocus', 'min' => 0]) }}
		@if ($errors->has('hours_overtime')) <p class="help-block">{{ $errors->first('hours_overtime') }}</p> @endif
		</div>
	</div>

	<div class="form-group col-md-6 @if ($errors->has('multiplier_overtime')) has-error @endif">
		{{ Form::labelRequired('multiplier_overtime', 'Multiplier:', ['class' => 'col-md-6 control-label', 'min' => 1]) }}
		<div class="col-md-6">
		{{ Form::number('multiplier_overtime', number_format($entry->multiplier_overtime, 1), ['class' => 'form-control', 'step' => 'any']) }}
		@if ($errors->has('multiplier_overtime')) <p class="help-block">{{ $errors->first('multiplier_overtime') }}</p> @endif
		</div>
	</div>

	<h2>Double Time</h2>
	
	<div class="form-group col-md-6 @if ($errors->has('hours_double')) has-error @endif">
		{{ Form::labelRequired('hours_double', 'Hours After:', ['class' => 'col-md-6 control-label', 'min' => 0]) }}
		<div class="col-md-6">
		{{ Form::number('hours_double', $value = null, ['class' => 'form-control']) }}
		@if ($errors->has('hours_double')) <p class="help-block">{{ $errors->first('hours_double') }}</p> @endif
		</div>
	</div>

	<div class="form-group col-md-6 @if ($errors->has('multiplier_double')) has-error @endif">
		{{ Form::labelRequired('multiplier_double', 'Multiplier:', ['class' => 'col-md-6 control-label', 'min' => 1]) }}
		<div class="col-md-6">
		{{ Form::number('multiplier_double', number_format($entry->multiplier_double, 1), ['class' => 'form-control', 'step' => 'any']) }}
		@if ($errors->has('multiplier_double')) <p class="help-block">{{ $errors->first('multiplier_double') }}</p> @endif
		</div>
	</div>

	<div class="form-group">
	<div class="col-md-offset-3 col-md-9">
	{{ Form::submit('Save Changes', ['class' => 'btn btn-primary']) }}
	</div>
	</div>

	@if (isset($entry->id)) {{ method_field('PUT') }} @endif
	{{ Form::close() }}
    </div>
</div>
@endsection