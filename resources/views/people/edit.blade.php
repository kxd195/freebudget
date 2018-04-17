@extends('layouts.app')

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript" src="{{ asset('js/people.edit.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/line_items.edit.js') }}"></script>
@endsection

@section('content')
<div class="small">
	<a href="{{ route('home') }}"><strong>Home</strong></a>
	&gt; <a href="{{ route('shows.edit', $entry->budget->show_id) }}"><strong>{{ $entry->budget->show->name }}</strong></a>
	&gt; <a href="{{ route('budgets.show', ['id' => $entry->budget_id, 'show_day_id' => isset($entry->day_id) ? $entry->day->id : 0]) }}"><strong>{{ $entry->budget->name }}</strong></a>
	&gt; {{ isset($entry->id) ? 'Modify an Existing' : 'Create a New' }} Person/Group
</div>

@component('components.messages')
@endcomponent
<div class="panel panel-primary">
<div class="panel-heading"><h1 class="panel-title">{{ isset($entry->id) ? 'Modify an Existing' : 'Create a New' }} Person/Group</h1></div>
<div class="panel-body">
	{{ Form::model($entry, [
		'route' => isset($entry->id) ? ['people.update', $entry->id] : 'people.store', 
		'class' => 'form-horizontal']) }}

    	<div class="form-group @if ($errors->has('day_id')) has-error @endif">
    		{{ Form::labelRequired('day_id', 'Day:', ['class' => 'col-md-3 control-label']) }}
    		<div class="col-md-9 form-inline">
    			{{ Form::select('day_id', $days, $entry->day_id, ['class' => 'form-control', 
    				'data-toggle' => 'tooltip', 'data-placement' => 'top',
    				'title' => 'Use this to move this person to another day/date']) }}
			<button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#dayModal">Move/copy to multiple days</button>
	    		@if ($errors->has('day_id')) <p class="help-block">{{ $errors->first('day_id') }}</p> @endif
    		</div>
    	</div>
    	
    	<div class="form-group @if ($errors->has('unit_id')) has-error @endif">
    		{{ Form::labelRequired('unit_id', 'Unit:', ['class' => 'col-md-3 control-label']) }}
    		<div class="col-md-9">
    			{{ Form::select('unit_id', $units, $entry->unit_id, ['class' => 'form-control']) }}
	    		@if ($errors->has('unit_id')) <p class="help-block">{{ $errors->first('unit_id') }}</p> @endif
    		</div>
    	</div>
    	
    	<hr role="separator" />

    	<div class="form-group">
    		{{ Form::label('scene', 'Scene:', ['class' => 'col-md-3 control-label']) }}
    		<div class="col-md-9">
    		<div class="radio form-inline">
        		<label>
        			{{ Form::radio('scene_option', 'none', count($scenes) === 0 || !isset($entry->scene), ['onclick' => 'toggleScene(this);']) }}
        			Not associated to a scene
        		</label>
    		</div>
    		@unless (count($scenes) === 0)
        		<div class="radio form-inline">
            		<label>
            			{{ Form::radio('scene_option', 'select', isset($entry->scene), ['onclick' => 'toggleScene(this);']) }}
            			Select existing:
                		{{ Form::select('scene', $scenes, $entry->scene, ['class' => 'form-control', 'placeholder' => 'Select...']) }}
            		</label>
        		</div>
    		@endunless
    		<div class="radio form-inline @if ($errors->has('scene_new')) has-error @endif">
        		<label>
        			{{ Form::radio('scene_option', 'new', false, ['onclick' => 'toggleScene(this);']) }}
        			Specify new:
            		@if ($errors->has('scene_new')) <p class="help-block">{{ $errors->first('scene_new') }}</p> @endif
            		{{ Form::text('scene_new', '', ['size' => 50, 'class' => 'form-control', 'placeholder' => 'Enter scene number(s) and/or description']) }}
        		</label>
    		</div>
    		</div>
    	</div>
    
    	<hr role="separator" />

    	<div class="form-group @if ($errors->has('description')) has-error @endif">
    		{{ Form::labelRequired('description', 'Person/Group Description:', ['class' => 'col-md-3 control-label']) }}
    		<div class="col-md-9">
    		{{ Form::text('description', $entry->description, ['class' => 'form-control']) }}
    		@if ($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
    		</div>
    	</div>
    
    <table class="table table-condensed">
    <thead>
        <tr>
        	<th id="qty_col" class="col-md-1 text-center">{{ Form::labelRequired('qty', 'Qty', ['class' => 'control-label']) }}</th>
        	<th id="rate_class_col" class="col-md-3 text-center">{{ Form::labelRequired('rate_class', 'Rate Class', ['class' => 'control-label']) }}</th>
        	<th id="hours_col" class="col-md-1 text-center">
        		{{ Form::labelRequired('hours', 'Hours', ['class' => 'control-label']) }}
        	</th>
        	<th id="payroll_col" class="col-md-2 text-center">{{ Form::label('', 'Payroll Hours', ['class' => 'control-label']) }}</th>
        	<th id="cost_col" class="col-md-2 text-center">{{ Form::label('cost', 'Cost', ['class' => 'control-label']) }}</th>
        	<th id="amount_col" class="col-md-2 text-center">{{ Form::label('', 'Amount', ['class' => 'control-label']) }}</th>
        	<th id="action_col" class="col-md-1 text-center">{{ Form::label('', 'Action', ['class' => 'control-label']) }}</th>
        </tr>
    </thead>
    <tbody>
    		@php $counter = 0; @endphp
    		@foreach ($entry->line_items as $item)
		@component('people.line-item-row', ['counter' => $counter, 'item' => $item, 'categories' => $categories])
		@endcomponent
    		@php $counter++; @endphp
        @endforeach
    </tbody>
    <tfoot>
		<tr>
			<td colspan="7"><a href="#" onclick="addRow(this);" class="btn btn-warning btn-xs" id="addRowButton">Add Another Entry</a></td>
		</tr>
		@component('people.line-item-row', ['categories' => $categories])
		@endcomponent
    </tfoot>
	</table>
	
	<hr role="separator" />
	<div class="form-group">
	<div class="col-md-offset-3 col-md-9">
	{{ Form::hidden('budget_id') }}
	{{ Form::submit('Save Changes', ['class' => 'btn btn-primary', 'id' => 'submitButton']) }}
	</div>
	</div>

	@if (isset($entry->id)) {{ method_field('PUT') }} @endif
	{{ Form::close() }}
	
	@component('components.delete', ['entry' => $entry, 'controller' => 'people', 'caption' => 'Delete Person/Group'])
	@endcomponent
</div>
</div>
@include('people.day-modal')
@endsection