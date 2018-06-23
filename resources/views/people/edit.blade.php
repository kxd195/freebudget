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
	&gt; <a href="{{ route('productions.edit', $entry->budget->production_id) }}"><strong>{{ $entry->budget->production->name }}</strong></a>
	&gt; <a href="{{ route('budgets.show', ['id' => $entry->budget_id]) }}"><strong>{{ $entry->budget->name }}</strong></a>
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
	
	<div class="form-group">
		{{ Form::label('scene', 'Scene:', ['class' => 'col-md-3 control-label']) }}
		<div class="col-md-9">
		<div class="radio form-inline">
			<label>
				{{ Form::radio('scene_option', 'none', count($entry->budget->scenes) === 0 || !isset($entry->scene), ['onclick' => 'toggleScene(this);']) }}
				Not associated to a scene
			</label>
		</div>
		@unless (count($entry->budget->scenes) === 0)
			<div>
				<div class="radio form-inline">
					<label>
						{{ Form::radio('scene_option', 'select', isset($entry->scene), ['onclick' => 'toggleScene(this);']) }}
						Select an existing scene:
						<select id="scene_id" name="scene_id" class="form-control" onchange="toggleScene(this);">
							<option disabled {{ !isset($entry->scene_id) ? "selected" : "" }}>Select...</option>
							@foreach($entry->budget->scenes as $scene)
								<option 
									data-location="{{ $scene->location }}"
									data-description="{{ $scene->description }}"
									data-notes="{{ $scene->notes }}"
									{{ $entry->scene_id === $scene->id ? "selected" : "" }}
									value="{{ $scene->id }}">{{ $scene->name }}</option>
							@endforeach
						</select>
					</label>
				</div>

				<div id="pane-scene-existing" class="small" style="display:none;">
				<div class="row">
					<div class="col-md-2 col-md-offset-1"><strong>Scene Name:</strong></div>
					<div class="col-md-3" id="scene-existing-name"></div>
					<div class="col-md-2 col-md-offset-1"><strong>Location:</strong></div>
					<div class="col-md-3" id="scene-existing-location"></div>
				</div>

				<div class="row">
					<div class="col-md-2 col-md-offset-1"><strong>Description:</strong></div>
					<div class="col-md-3" id="scene-existing-description"></div>
				</div>

				<div class="row">
					<div class="col-md-2 col-md-offset-1"><strong>Notes:</strong></div>
					<div class="col-md-3" id="scene-existing-notes"></div>
				</div>
				</div>
			</div>
		@endunless
		<div class="@if ($errors->has('scene[name]')) has-error @endif">
			<div class="radio">
			<label>
				{{ Form::radio('scene_option', 'new', false, ['onclick' => 'toggleScene(this);']) }}
				Create a new scene
			</label>
			</div>

			<div id="pane-scene-new" style="display:none;">
			<div class="form-group">
				{{ Form::labelRequired('scene[name]', 'Scene Name:', ['class' => 'col-md-2 col-md-offset-1 control-label']) }}
				<div class="col-md-9">
				{{ Form::text('scene[name]', '', ['size' => 50, 'class' => 'form-control']) }}
				@if ($errors->has('scene[name]')) <p class="help-block">{{ $errors->first('scene[name]') }}</p> @endif
				</div>
			</div>

			<div class="form-group">
				{{ Form::label('scene[description]', 'Description:', ['class' => 'col-md-2 col-md-offset-1 control-label']) }}
				<div class="col-md-9">
				{{ Form::text('scene[description]', '', ['size' => 50, 'class' => 'form-control']) }}
				</div>
			</div>

			<div class="form-group">
				{{ Form::label('scene[location]', 'Location:', ['class' => 'col-md-2 col-md-offset-1 control-label']) }}
				<div class="col-md-9">
				{{ Form::text('scene[location]', '', ['size' => 50, 'class' => 'form-control']) }}
				</div>
			</div>

			<div class="form-group">
				{{ Form::label('scene[notes]', 'Notes:', ['class' => 'col-md-2 col-md-offset-1 control-label']) }}
				<div class="col-md-9">
				{{ Form::text('scene[notes]', '', ['size' => 50, 'class' => 'form-control']) }}
				</div>
			</div>
			</div>
		</div>
		</div>
	</div>

	<hr role="separator" />

	<table class="table table-condensed">
	<thead>
		<tr>
			<th id="qty_col" class="col-md-1 text-center">{{ Form::labelRequired('qty', 'Qty', ['class' => 'control-label']) }}</th>
			<th id="description_col" class="col-md-2 text-center">{{ Form::labelRequired('description', 'Description', ['class' => 'control-label']) }}</th>
			<th id="rate_class_col" class="col-md-3 text-center">{{ Form::labelRequired('rate_class', 'Rate Class', ['class' => 'control-label']) }}</th>
			<th id="hours_col" class="col-md-1 text-center">
				{{ Form::labelRequired('hours', 'Hours', ['class' => 'control-label']) }}
			</th>
			<th id="payroll_col" class="col-md-1 text-center">{{ Form::label('', 'Payroll Hrs', ['class' => 'control-label']) }}</th>
			<th id="cost_col" class="col-md-2 text-center">{{ Form::label('cost', 'Cost', ['class' => 'control-label']) }}</th>
			<th id="amount_col" class="col-md-2 text-center">{{ Form::label('', 'Amount', ['class' => 'control-label']) }}</th>
		</tr>
	</thead>
	<tbody>
		@php $counter = 0; @endphp
		@foreach($entry->modifiable_people as $person)
			@component('people.line-item-row', ['counter' => $counter, 'wrangler' => $entry->budget->production->wrangler_rate, 'person' => $person, 'categories' => $categories])
			@endcomponent
			@php $counter++; @endphp
		@endforeach
	</tbody>
	<tfoot>
		<tr>
			<td colspan="7"><a href="#" onclick="addRow(this);" class="btn btn-warning btn-xs" id="addRowButton">Add Another Entry</a></td>
		</tr>
		@component('people.line-item-row', ['wrangler' => $entry->budget->production->wrangler_rate, 'categories' => $categories])
		@endcomponent
	</tfoot>
	</table>
	
	<hr role="separator" />
	<div class="form-group">
	<div class="col-md-offset-3 col-md-9">
	{{ Form::hidden('budget_id') }}
	@php $counter = 0; @endphp
	@foreach($entry->modifiable_people as $person)
		{{ Form::hidden("original_people[$person->id]", true) }}
		@php $counter++; @endphp
	@endforeach
	{{ Form::submit('Save Changes', ['class' => 'btn btn-primary', 'id' => 'submitButton']) }}
	</div>
	</div>

	@if (isset($entry->id)) {{ method_field('PUT') }} @endif
	{{ Form::close() }}
</div>
</div>
@include('people.day-modal')
@endsection