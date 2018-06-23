@extends('layouts.app')

@section('scripts')
<script type="text/javascript" src="{{ asset('js/productions.edit.js') }}"></script>
@endsection

@section('content')
<div class="small">
	<a href="{{ route('home') }}"><strong>Home</strong></a>
	&gt; {{ $entry->name or 'Create New Production' }} 
</div>
<div class="panel panel-primary">
    <div class="panel-heading"><h1 class="panel-title">{{ $entry->name or 'Create New Production' }}</h1></div>
    <div class="panel-body">
	{{ Form::model($entry, [
		'route' => isset($entry->id) ? ['productions.update', $entry->id] : 'productions.store', 
		'class' => 'form-horizontal']) }}    

	<div class="form-group @if ($errors->has('name')) has-error @endif">
		{{ Form::labelRequired('name', 'Production Name:', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-9">
		{{ Form::text('name', $value = null, ['class' => 'form-control', 'autofocus' => 'autofocus']) }}
		@if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
		</div>
	</div>

	<div class="form-group @if ($errors->has('type')) has-error @endif">
		{{ Form::labelRequired('type', 'Type:', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-9">
		{{ Form::select('type', App\Production::getTypes(), $entry->type, ['class' => 'form-control', 'placeholder' => 'Select...', 'onchange' => 'toggleType()']) }}
		@if ($errors->has('type')) <p class="help-block">{{ $errors->first('type') }}</p> @endif
		</div>
	</div>

	<div class="form-group @if ($errors->has('qty')) has-error @endif" id="pane-qty">
		{{ Form::label('qty', 'Episodes/Days:', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-3">
		{{ Form::number('qty', $entry->qty = 0, ['class' => 'form-control', 'step' => 'any', 'min' => 0]) }}
		</div>
		@if ($errors->has('qty')) <p class="help-block">{{ $errors->first('qty') }}</p> @endif
	</div>

	<div class="form-group" id="pane-season">
		{{ Form::label('season', 'Season:', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-5">
		{{ Form::text('season', $value = null, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		{{ Form::label('', 'Work Week Schedule:', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-9 form-inline">
		<div class="checkbox">
		<label style="margin-right:10px;">
		{{ Form::checkbox('work_sun', '1') }}
		Sun
		</label>
		<label style="margin-right:10px;">
		{{ Form::checkbox('work_mon', '1') }}
		Mon
		</label>
		<label style="margin-right:10px;">
		{{ Form::checkbox('work_tue', '1') }}
		Tue
		</label>
		<label style="margin-right:10px;">
		{{ Form::checkbox('work_wed', '1') }}
		Wed
		</label>
		<label style="margin-right:10px;">
		{{ Form::checkbox('work_thu', '1') }}
		Thu
		</label>
		<label style="margin-right:10px;">
		{{ Form::checkbox('work_fri', '1') }}
		Fri
		</label>
		<label style="margin-right:10px;">
		{{ Form::checkbox('work_sat', '1') }}
		Sat
		</label>
		</div>
		</div>
	</div>
	
    	<div class="form-group @if ($errors->has('assistant_rate')) has-error @endif">
    		{{ Form::label('assistant_rate', 'Assistant Rate:', ['class' => 'col-md-3 control-label']) }}
    		<div class="col-md-3">
    		<div class="input-group">
        		<span class="input-group-addon">$</span>
        		{{ Form::text('assistant_rate', $value = null, 
        			['class' => 'form-control text-right',
            		'data-mask' => '000,000,000,000,000.00', 'data-mask-reverse' => 'true']) }}
        		<span class="input-group-addon">per week</span>
    		</div>
    		</div>
        	@if ($errors->has('assistant_rate')) <p class="help-block">{{ $errors->first('assistant_rate') }}</p> @endif
    	</div>

    	<div class="form-group @if ($errors->has('wrangler_rate')) has-error @endif">
    		{{ Form::label('wrangler_rate', 'Key Wrangler Rate:', ['class' => 'col-md-3 control-label']) }}
    		<div class="col-md-3">
    		<div class="input-group">
        		<span class="input-group-addon">$</span>
        		{{ Form::text('wrangler_rate', $value = null, 
        			['class' => 'form-control text-right',
            		'data-mask' => '000,000,000,000,000.00', 'data-mask-reverse' => 'true']) }}
        		<span class="input-group-addon">per hour</span>
    		</div>
    		</div>
        	@if ($errors->has('wrangler_rate')) <p class="help-block">{{ $errors->first('wrangler_rate') }}</p> @endif
    	</div>

    	<div class="form-group @if ($errors->has('wrangler_addl_rate')) has-error @endif">
    		{{ Form::label('wrangler_addl_rate', 'Addl. Wrangler Rate:', ['class' => 'col-md-3 control-label']) }}
    		<div class="col-md-3">
    		<div class="input-group">
        		<span class="input-group-addon">$</span>
        		{{ Form::text('wrangler_addl_rate', $value = null, 
        			['class' => 'form-control text-right',
            		'data-mask' => '000,000,000,000,000.00', 'data-mask-reverse' => 'true']) }}
        		<span class="input-group-addon">per hour</span>
    		</div>
    		</div>
        	@if ($errors->has('wrangler_addl_rate')) <p class="help-block">{{ $errors->first('wrangler_addl_rate') }}</p> @endif
    	</div>

	<div class="form-group @if ($errors->has('num_union')) has-error @endif">
		{{ Form::label('num_union', 'Daily Union Requirements:', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-2">
		{{ Form::number('num_union', $value = 0, ['class' => 'form-control', 'step' => 'any', 'min' => 0]) }}
		</div>
		@if ($errors->has('num_union')) <p class="help-block">{{ $errors->first('num_union') }}</p> @endif
	</div>


	<div class="form-group">
	<div class="col-md-offset-3 col-md-9">
	{{ Form::submit('Save Changes', ['class' => 'btn btn-primary']) }}
	</div>
	</div>

	@if (isset($entry->id)) {{ method_field('PUT') }} @endif
	{{ Form::close() }}
	
	@component('components.delete', ['entry' => $entry, 'controller' => 'productions', 'caption' => 'Delete Production'])
	@endcomponent

	@if (isset($entry->id))	
	<hr role="separator" />

	<div class="form form-horizontal">
	<div class="form-group">
		{{ Form::label('', 'Budgets', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-9 form-control-static">
		<ul>
			@foreach ($entry->budgets as $budget)
				<li><a href="{{ route('budgets.show', $budget->id) }}">{{ $budget->name }}</a></li>
			@endforeach
			<li><a href="{{ route('budgets.create', ['production_id' => $entry->id]) }}"><em>Create a New Budget</em></a></li>
		</ul>
		</div>
	</div>
	</div>
	@endif
	
    </div>
</div>
@endsection