
@foreach ($budget->days as $day)
	@php
		$dayTotal = 0;
	@endphp

	<div class="panel panel-primary" id="dayTable-{{ $day->id }}" style="page-break-inside:avoid;">
		<div class="panel-heading">
			<h4 class="panel-title" style="margin-top:5px; margin-bottom:5px;"><a name="day-{{ $day->id }}">{{ $day->generateName() }}</a>
			@unless ($readonly)
			<div class="pull-right">
				<a href="{{ route('people.create', ['budget_id' => $budget->id, 'day_id' => isset($day) ? $day->id : '']) }}" class="btn btn-info btn-xs no-print">Add Entries</a>
				@unless ($day->is_default_day)
				<a href="{{ route('days.edit', $day->id) }}" class="btn btn-info btn-xs no-print">Modify Day</a>
				@endunless
			</div>
			@endunless
			</h4>

			@unless ($day->is_default_day)
				<div class="row">
					<div class="col-xs-2"><strong>Crew Call:</strong></div>
					<div class="col-xs-10" style="font-weight:normal;">{{ $day->crew_call }}</div>
				</div>
				
				<div class="row">
					<div class="col-xs-2"><strong>Location(s):</strong></div>
					<div class="col-xs-10" style="font-weight:normal;">{{ $day->location }}</div>
				</div>

				<div class="row">
					<div class="col-xs-2"><strong>Notes:</strong></div>
					<div class="col-xs-10" style="font-weight:normal;">{{ $day->notes }}</div>
				</div>
			@endunless
		</div>

		<div class="panel-body">

		<table class="table table-condensed day-table small" style="width:100%;">
		@foreach ($day->distinctUnits() as $unit)
		<tbody>
			<tr class="bg-info">
				<td colspan="12"><h4>{{ $unit->name }}</h4></td>
			</tr>
			<tr class="text-center">
				<th id="scene_col" class="col-md-1">Scene</th>
				<th id="scene_desc_col" class="col-md-2">Sc. Description</th>
				<th id="scene_location_col" class="col-md-1">Sc. Location(s)</th>
				<th id="scene_actions_col" class="col-md-1 no-print">Sc. Actions</th>
				<th id="qty_col" class="col-md-1 text-center">Qty</th>
				<th id="description_col" class="col-md-2">BG Description/Label</th>
				<th id="rateclass_col" class="col-md-1 text-center">Rate Class</th>
				<th id="hours_col" class="col-md-1 text-center">Hours</th>
				<th id="payroll_col" class="col-md-1 text-center">P. Hrs</th>
				<th id="cost_col" class="col-md-1 text-center">Cost</th>
				<th id="amount_col" class="col-md-1 text-center">Total</th>
				@unless ($readonly)
					<th id="action_col" class="col-md-1 text-center no-print">Entry Actions</th>
				@endunless
			</tr>
			@foreach ($unit->people($day->id) as $person)
				<tr>
					@if ($person->rateclass != null && $person->rateclass->code === 'AS')
						<td headers="scene_col" class="text-nowrap" colspan="3">
								<em>Labour</em>
						</td>
					@else
						<td headers="scene_col" class="text-nowrap">
							@if (isset($person->scene))
								{{ (isset($person->scene->episode) ? $person->scene->episode . ', ' : '') . $person->scene->name }}
							@else
								{{ 'N/A' }}
							@endif
						</td>
						<td headers="scene_desc_col">{{ isset($person->scene) ? $person->scene->description : '' }}</td>
						<td headers="scene_location_col">{{ isset($person->scene) ? $person->scene->location : '' }}</td>

					@endif
					@unless ($readonly)
						<td headers="scene_actions_col" class="text-center text-nowrap no-print">
							@isset($person->scene)
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
									data-episode="{{ $person->scene->episode }}"
									data-scene-name="{{ $person->scene->name }}"
									data-location="{{ $person->scene->location }}" 
									data-description="{{ $person->scene->description }}"
									data-notes="{{ $person->scene->notes }}">
									<span class="glyphicon glyphicon-film" title="Modify scene details only" data-toggle="tooltip"></span>
								</button>
							@endisset
						</td>
					@endunless
					<td headers="qty_col" class="text-center">{{ $person->qty }}</td>
					<td headers="description_col">{{ $person->description }}</td>
					<td headers="rateclass_col" class="text-center {{ $person->rateclass->bgcolor or '' }}">{!! $person->rateclass != null ? $person->rateclass->getCodeAbbr() : '' !!}</td>
					<td headers="hours_col" class="text-right">
						{{ $person->rateclass != null && $person->rateclass->is_daily ? 'Daily' : number_format($person->hours, 1) }}
					</td>
					<td headers="payroll_col" class="text-right">
						{{ $person->rateclass != null && $person->rateclass->is_daily ? 'Daily' : number_format($person->calcPayroll(), 1) }}
					</td>
					<td headers="cost_col" class="text-right">
						{{ number_format($person->cost, 2) }}
						@if ($person->cost_overridden)
						<span title="Cost Overridden" data-toggle="tooltip" class="glyphicon glyphicon-flash text-danger"></span>
						@endif

						@if ($person->cost_secondrate)
						<span title="50% Rate" data-toggle="tooltip" class="glyphicon glyphicon-circle-arrow-down text-danger"></span>
						@endif
					</td>
					<td headers="amount_col" class="text-right line-item-amount">{{ number_format($person->calcAmount(), 2) }}</td>
					@unless ($readonly)
						<td headers="action_col" class="text-center text-nowrap no-print">
						<a href="{{ route('people.edit', [$person->id, 'budget_id' => $budget->id]) }}" class="btn btn-primary btn-xs" title="Modify this entry" data-toggle="tooltip">
							<span class="glyphicon glyphicon-pencil"></span>
						</a>
						<a href="{{ route('people.edit', [$person->id, 'budget_id' => $budget->id, 'copy' => true]) }}" class="btn btn-primary btn-xs" title="Copy this entry" data-toggle="tooltip">
							<span class="glyphicon glyphicon-copy"></span>
						</a>

						<input type="checkbox" name="delete_entry_id[]" value="{{ $person->id }}" title="Select to delete" data-toggle="tooltip" />
						</td>
					@endunless
				</tr>
			@endforeach
			</tbody>
		@endforeach
		@if (count($day->people) > 0)
			<tfoot>
			<tr class="text-primary" style="height:3em;">
				<td class="no-print"></td>
				<td colspan="3" class="text-right"><strong>QTY Total:</strong></td>
				<td class="text-center"><strong>{{ $day->calcTotalQuantity() }}</strong></td>
				<td colspan="5" class="text-right"><strong>Day Total:</strong></td>
				<td headers="amount_col" class="text-right"><strong>{{ number_format($day->calcTotalAmount(), 2) }}</strong></td>
				@unless ($readonly)
					<td></td>
				@endunless
			</tr>
			</tfoot>
		@endif
		</table>
		</div>
	</div>
@endforeach
