<table class="table table-condensed day-table" style="width:100%;" id="dayTable">
<thead>
	<tr class="text-center small">
		<th id="qty_col" class="col-md-1 text-center">Qty</th>
		<th id="description_col">Description</th>
		<th id="rateclass_col" class="col-md-1 text-center">Rate Class</th>
		<th id="hours_col" class="col-md-1 text-center">Hours</th>
		<th id="payroll_col" class="col-md-1 text-center">Payroll Hrs</th>
		<th id="cost_col" class="col-md-1 text-center">Cost</th>
		<th id="amount_col" class="col-md-2 text-center">Total</th>
		@unless ($readonly)
			<th id="action_col" class="col-md-1 text-center">Actions</th>
		@endunless
	</tr>
</thead>

@foreach ($budget->days as $day)
	@php
		$dayTotal = 0;
	@endphp
	<tbody data-day-id="{{ $day->id }}">
	<tr><th colspan="8" class="bg-primary">
		<h4 class="bg-primary text-primary" style="margin-top:5px; margin-bottom:5px;">
			{{ $day->generateName() }}
			@unless ($readonly)
			<div class="pull-right">
				<a href="{{ route('people.create', ['budget_id' => $budget->id, 'day_id' => isset($day) ? $day->id : '']) }}" class="btn btn-info btn-xs">Add Entries</a>
				@unless ($day->id === 0)
				<a href="{{ route('days.edit', $day->id) }}" class="btn btn-info btn-xs">Modify Day</a>
				@endunless
			</div>
			@endunless
		</h4>
		<div class="row small">

		@isset ($day->crew_call)
			<div class="col-md-4 small">
				<div class="col-xs-3"><strong>Crew Call:</strong></div>
				<div class="col-xs-9" style="font-weight:normal;">{{ $day->crew_call }}</div>
			</div>
		@endisset
		
		@isset ($day->notes)
			<div class="col-md-8 small">
				<div class="col-xs-1"><strong>Notes:</strong></div>
				<div class="col-xs-11" style="font-weight:normal;">{{ $day->notes }}</div>
			</div>
		@endisset
		</div>
	</th></tr>
	@php 
		$last_unit = null;
		$last_scene = null;
	@endphp

	@foreach ($day->people as $person)
		@if (!isset($last_unit) || $person->unit_id !== $last_unit)
			<tr class="bg-info"><th colspan="8">{{ $person->unit->name }}</th></tr>
			@php 
				$last_unit = $person->unit_id;
				$last_scene = null;
			@endphp
		@endif
		
		@if ($person->scene_id !== $last_scene)
			<tr>
			<td colspan="7" class="bg-success">
				<strong><em>Scene: {{ $person->scene->name }}</em></strong>
				<div class="row small">

				@isset ($person->scene->description)
					<div class="col-md-4 small">
						<div class="col-xs-3"><strong>Description:</strong></div>
						<div class="col-xs-9" style="font-weight:normal;">{{ $person->scene->description }}</div>
					</div>
				@endisset
				
				@isset ($person->scene->location)
					<div class="col-md-4 small">
						<div class="col-xs-3"><strong>Location:</strong></div>
						<div class="col-xs-9" style="font-weight:normal;">{{ $person->scene->location }}</div>
					</div>
				@endisset

				@isset ($person->scene->notes)
					<div class="col-md-4 small">
						<div class="col-xs-3"><strong>Notes:</strong></div>
						<div class="col-xs-9" style="font-weight:normal;">{{ $person->scene->notes }}</div>
					</div>
				@endisset
				</div>
			</td>
			@unless ($readonly)
				<td headers="action_col" class="bg-success text-center text-nowrap">
					<a href="{{ route('people.edit', [$person->id, 'budget_id' => $budget->id, 'whole_scene' => true]) }}" class="btn btn-primary btn-xs" title="Modify all entries in this scene" data-toggle="tooltip">
						<span class="glyphicon glyphicon-pencil"></span>
					</a>
					<a href="{{ route('people.edit', [$person->id, 'budget_id' => $budget->id, 'whole_scene' => true, 'copy' => true]) }}" class="btn btn-primary btn-xs" title="Copy all entries in this scene" data-toggle="tooltip">
						<span class="glyphicon glyphicon-copy"></span>
					</a>
					<button type="button" class="btn btn-warning btn-xs"
						data-toggle="modal" data-target="#sceneModal" 
						data-day-id="{{ $day->id }}"
						data-day-name="{{ $day-> generateName() }}"
						data-scene="{{ $person->scene_id }}"
						data-scene-name="{{ $person->scene->name }}"
						data-location="{{ $person->scene->location }}" 
						data-description="{{ $person->scene->description }}"
						data-notes="{{ $person->scene->notes }}">
						<span class="glyphicon glyphicon-film" title="Modify scene details only" data-toggle="tooltip"></span>
					</button>
				</td>
			@endunless
			@php 
				$last_scene = $person->scene_id;
			@endphp
			</tr>
		@endif

		<tr class="small">
			<td headers="qty_col" class="text-center">{{ $person->qty }}x</td>
			<td headers="description_col">{{ $person->description }}</td>
			<td headers="rateclass_col" class="text-center {{ $person->rateclass->bgcolor or '' }}">{!! $person->rateclass->getCodeAbbr() !!}</td>
			<td headers="hours_col" class="text-right {{ $person->rateclass->bgcolor or '' }}">{{ number_format($person->hours, 1) }}</td>
			<td headers="payroll_col" class="text-right {{ $person->rateclass->bgcolor or '' }}">{{ number_format($person->calcPayroll(), 1) }}</td>
			<td headers="cost_col" class="text-right {{ $person->rateclass->bgcolor or '' }}">
				{{ number_format($person->cost, 2) }}
				@if ($person->cost_overridden)
				<span title="Cost Overridden" data-toggle="tooltip" class="glyphicon glyphicon-flash text-danger"></span>
				@endif

				@if ($person->cost_secondrate)
				<span title="50% Rate" data-toggle="tooltip" class="glyphicon glyphicon-circle-arrow-down text-danger"></span>
				@endif
			</td>
			<td headers="amount_col" class="text-right line-item-amount {{ $person->rateclass->bgcolor or '' }}">{{ number_format($person->calcAmount(), 2) }}</td>
			@unless ($readonly)
				<td headers="action_col" class="text-center text-nowrap">
				{{ Form::model($person, [ 'route' => [ 'people.destroy', $person->id], 'class' => 'form-inline' ]) }}
					<a href="{{ route('people.edit', [$person->id, 'budget_id' => $budget->id]) }}" class="btn btn-primary btn-xs" title="Modify this entry" data-toggle="tooltip">
						<span class="glyphicon glyphicon-pencil"></span>
				</a>
					<a href="{{ route('people.edit', [$person->id, 'budget_id' => $budget->id, 'copy' => true]) }}" class="btn btn-primary btn-xs" title="Copy this entry" data-toggle="tooltip">
						<span class="glyphicon glyphicon-copy"></span>
				</a>
				{{ method_field('DELETE') }}
					<button type="submit" class="btn btn-xs btn-danger" title="Delete this entry" data-toggle="tooltip"
						onclick="return confirm('{{ $message or 'Are you sure you would like to delete this entry?' }}');">
							<span class="glyphicon glyphicon-trash"></span>
					</button>
				{{ Form::close() }}
				</td>
			@endunless
		</tr>
	@endforeach

	<tr class="text-primary" style="height:3em;">
		<td colspan="6" class="text-right"><strong>Day Total:</strong></td>
		<td headers="amount_col" class="text-right"><strong>{{ number_format($day->calcTotalAmount(), 2) }}</strong></td>
		@unless ($readonly)
			<td></td>
		@endunless
	</tr>

	</tbody>
@endforeach
</table>
