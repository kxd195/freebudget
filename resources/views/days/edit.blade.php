@extends('layouts.app')

@section('scripts')
<script type="text/javascript" src="{{ asset('js/days.edit.js') }}"></script>
<script type="text/javascript">
var existingDays = [ @foreach ($entry->budget->days as $day) "{{ $day->actualdate->format('Y-m-d') }}", @endforeach ];
</script>
@endsection

@section('content')
<div class="small">
	<a href="{{ route('home') }}"><strong>Home</strong></a>
	&gt; <a href="{{ route('shows.edit', $entry->budget->show_id) }}"><strong>{{ $entry->budget->show->name }}</strong></a>
	&gt; <a href="{{ route('budgets.show', ['id' => $entry->budget_id, 'show_day_id' => $entry->id]) }}"><strong>{{ $entry->budget->name }}</strong></a>
	&gt; {{ isset($entry->id) ? 'Modify an Existing' : 'Create a New' }} Day
</div>
<div class="panel panel-primary">
<div class="panel-heading"><h1 class="panel-title">{{ isset($entry->id) ? 'Modify an Existing' : 'Create a New' }} Day</h1></div>
<div class="panel-body">
	{{ Form::model($entry, [
		'route' => isset($entry->id) ? ['days.update', $entry->id] : 'days.store', 
		'class' => 'form-horizontal']) }}

    <div class="form-group @if ($errors->has('actualdate')) has-error @endif">
        {{ Form::labelRequired('actualdate', 'Actual Date:', ['class' => 'col-md-3 control-label']) }}
        <div class="col-md-3">
        <div class="input-group date" data-provide="datepicker">
            {{ Form::text('actualdate', isset($entry->actualdate) ? $entry->actualdate->format('Y-m-d') : '', 
                ['class' => 'form-control', 'onchange' => 'return checkDateConflict();']) }}
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
            </div>
        </div>  
        </div>
        @if ($errors->has('actualdate')) <p class="help-block">{{ $errors->first('actualdate') }}</p> @endif
    </div>

	<div class="form-group @if ($errors->has('name')) has-error @endif">
		{{ Form::labelRequired('name', 'Name:', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-3">
		<div class="input-group">
    <span class="input-group-addon">Day</span>
		{{ Form::text('name', $entry->name, ['class' => 'form-control']) }}
		</div>
		@if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
		</div>
	</div>
    
    <div id="pane-dateConlictWarning" class="row hidden">
    	<div class="col-md-9 col-md-offset-3">
    	<div class="alert alert-danger">
    		<strong>WARNING:</strong> Selected date already exists! If you save these changes, it will move/merge all existing personnel &amp; line items with the selected date.
    	</div>
    	</div>
    </div>

    	<div class="form-group @if ($errors->has('location')) has-error @endif">
    		{{ Form::label('location', 'Location:', ['class' => 'col-md-3 control-label']) }}
    		<div class="col-md-9">
    		{{ Form::text('location', $entry->location, ['class' => 'form-control']) }}
    		@if ($errors->has('location')) <p class="help-block">{{ $errors->first('location') }}</p> @endif
    		</div>
    	</div>

    	<div class="form-group @if ($errors->has('crew_call')) has-error @endif">
    		{{ Form::label('crew_call', 'Estimated Crew Call:', ['class' => 'col-md-3 control-label']) }}
    		<div class="col-md-9">
    		{{ Form::text('crew_call', $entry->crew_call, ['class' => 'form-control']) }}
    		@if ($errors->has('crew_call')) <p class="help-block">{{ $errors->first('crew_call') }}</p> @endif
    		</div>
    	</div>
    
	<div class="form-group @if ($errors->has('notes')) has-error @endif">
		{{ Form::label('notes', 'Notes:', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-9">
		{{ Form::textarea('notes', $value = null, ['class' => 'form-control', 'rows' => 4]) }}
		@if ($errors->has('notes')) <p class="help-block">{{ $errors->first('notes') }}</p> @endif
		</div>
	</div>

	<div class="form-group">
	<div class="col-md-offset-3 col-md-9">
	{{ Form::hidden('budget_id') }}
	{{ Form::hidden('originaldate', isset($entry->actualdate) ? $entry->actualdate->format('Y-m-d') : '') }}
	{{ Form::submit('Save Changes', ['class' => 'btn btn-primary']) }}
	</div>
	</div>

	@if (isset($entry->id)) {{ method_field('PUT') }} @endif
	{{ Form::close() }}
	
	@component('components.delete', ['entry' => $entry, 'controller' => 'days', 'message' => 'Are you sure you would like to delete this day?\n\nWARNING: Deleting this day will remove all line items!'])
	@endcomponent
</div>
</div>
@endsection