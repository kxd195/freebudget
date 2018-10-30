@extends('layouts.app')

@section('scripts')
<script type="text/javascript" src="{{ asset('js/budgets.edit.js') }}"></script>
@endsection

@section('content')
@component('components.messages')
@endcomponent

<div>
	<a href="{{ route('home') }}"><strong>Home</strong></a>
	&gt; <a href="{{ route('productions.edit', $entry->production_id) }}"><strong>{{ $entry->production->name }}</strong></a>
	&gt; {{ isset($entry->id) ? 'Modify an Existing' : 'Create a New' }} Budget
</div>
<div class="panel panel-primary">
    <div class="panel-heading"><h1 class="panel-title">{{ isset($entry->id) ? 'Modify an Existing' : 'Create a New' }} Budget</h1></div>
    <div class="panel-body">
	{{ Form::model($entry, [
		'route' => isset($entry->id) ? ['budgets.update', $entry->id] : 'budgets.store', 
		'class' => 'form-horizontal']) }}    

    	<div class="form-group">
    		{{ Form::label('', 'Production Name:', ['class' => 'col-md-3 control-label']) }}
    		<div class="col-md-9 form-control-static">{{ $entry->production->name }}</div>
    	</div>

	<div class="form-group @if ($errors->has('name')) has-error @endif">
		{{ Form::labelRequired('name', 'Script Name:', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-9">
		{{ Form::text('name', $value = null, ['class' => 'form-control']) }}
		@if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
		</div>
	</div>

	<div class="form-group @if ($errors->has('episode')) has-error @endif">
		{{ Form::label('episode', 'Episode:', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-3">
		{{ Form::text('episode', $value = null, ['class' => 'form-control']) }}
		@if ($errors->has('episode')) <p class="help-block">{{ $errors->first('episode') }}</p> @endif
		</div>
	</div>

	<div class="form-group @if ($errors->has('startdate')) has-error @endif">
		{{ Form::labelRequired('startdate', 'Shoot Day 1:', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-3">
        <div class="input-group date" data-provide="datepicker">
			{{ Form::text('startdate', $entry->startdate, ['class' => 'form-control']) }}
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
            </div>
        </div>  
		</div>
		@if ($errors->has('startdate')) <p class="help-block">{{ $errors->first('startdate') }}</p> @endif
	</div>

	<div class="form-group @if ($errors->has('enddate')) has-error @endif">
		{{ Form::labelRequired('', 'Shoot End Date OR # of Shoot Days:', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-9 form-inline">
			@isset ($entry->id)
        		<div class="alert alert-info">
        			<p><strong>Did you know?</strong> Auto-create only works when creating a new budget in order to avoid overwriting existing data.</p>
        		</div>
        		@endisset
        		
        		<div class="form-group col-md-12">
        		<div class="radio">
        		<label>
        			{{ Form::radio('until', 'enddate', isset($entry->enddate) || !isset($entry->id), ['onclick' => 'toggleUntilDate(this);']) }}
        			Until/End Date:
        		</label>
            <div class="input-group date" data-provide="datepicker">
    				{{ Form::text('enddate', $entry->enddate, ['class' => 'form-control', 'onclick' => 'toggleUntilDate(this);']) }}
                <div class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </div>
            </div>  
        		@if ($errors->has('enddate')) <p class="help-block">{{ $errors->first('enddate') }}</p> @endif
        		</div>
        		</div>

        		<div class="form-group col-md-12">
        		<div class="radio">
        		<label>
        			{{ Form::radio('until', 'num_days', isset($entry->num_days) && $entry->num_days !== 0, ['onclick' => 'toggleUntilDate(this);']) }}
        			Number of Days:
        		</label>
			<div class="input-group">
        			{{ Form::number('num_days', $entry->num_days, ['class' => 'form-control', 'step' => 'any', 'min' => 0, 'onclick' => 'toggleUntilDate(this);']) }}
				<div class="input-group-addon">days</div>
                </div>  
        		@if ($errors->has('enddate')) <p class="help-block">{{ $errors->first('enddate') }}</p> @endif
        		</div>
        		</div>
        		
		</div>
	</div>

	<div class="form-group">
	<div class="col-md-offset-3 col-md-9">
	{{ Form::hidden('production_id') }}
	{{ Form::submit('Save Changes', ['class' => 'btn btn-primary']) }}
	</div>
	</div>

	@if (isset($entry->id)) {{ method_field('PUT') }} @endif
	{{ Form::close() }}
	
	@component('components.delete', ['entry' => $entry, 'controller' => 'budgets'])
	@endcomponent
    </div>
</div>
@endsection