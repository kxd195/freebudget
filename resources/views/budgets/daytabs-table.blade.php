<table class="table table-condensed day-table" style="width:100%;" data-date="{{ $day->generateName() }}">
<thead>
    <tr class="text-center">
    	@unless ($readonly)
    		<th id="action_col" class="col-md-1 text-center">Actions</th>
    	@endunless
    	<th id="description_col">Description</th>
    	<th id="rateclass_col" class="col-md-1 text-center">Rate Class</th>
    	<th id="qty_col" class="col-md-1 text-center">Qty</th>
    	<th id="hours_col" class="col-md-1 text-center">Hours</th>
    	<th id="payroll_col" class="col-md-1 text-center">Payroll Hours</th>
    	<th id="cost_col" class="col-md-1 text-center">Cost</th>
    	<th id="amount_col" class="col-md-1 text-center">Amount</th>
    </tr>
</thead>
<tbody>

@foreach ($people_entries as $person)
	@if (!isset($last_unit) || $person->unit->name !== $last_unit)
		<tr><th colspan="8" class="bg-primary text-primary">{{ $person->unit->name }}</th></tr>
		@php 
			$last_unit = $person->unit->name;
			$last_scene = null;
		@endphp
	@endif
	
	@if ($person->scene !== $last_scene)
		<tr><td colspan="8" class="bg-info">
			<em>Scene: {{ $person->scene }}</em>
            @unless ($readonly)
    			<button type="button" class="btn btn-primary btn-xs pull-right"
    				data-toggle="modal" data-target="#sceneModal" 
    				data-day-id="{{ $day->id }}" data-scene="{{ $person->scene }}">Modify Scene</button>
            @endunless

		</td></tr>
		@php 
			$last_scene = $person->scene;
		@endphp
	@endif

    <tr class="small">
	@unless ($readonly)
    	<td headers="action_col" class="text-center text-nowrap" rowspan="{{ count($person->line_items) + 1 }}">
        {{ Form::model($person, [ 'route' => [ 'people.destroy', $person->id], 'class' => 'form-inline' ]) }}
    		<a href="{{ route('people.edit', [$person->id, 'budget_id' => $budget->id]) }}" class="btn btn-primary btn-xs" title="Modify" data-toggle="tooltip">
    			<span class="glyphicon glyphicon-pencil"></span>
		</a>
    		<a href="{{ route('people.edit', [$person->id, 'budget_id' => $budget->id, 'copy' => true]) }}" class="btn btn-primary btn-xs" title="Copy" data-toggle="tooltip">
    			<span class="glyphicon glyphicon-copy"></span>
		</a>
        {{ method_field('DELETE') }}
    		<button type="submit" class="btn btn-xs btn-danger" title="Delete" data-toggle="tooltip"
    			onclick="return confirm('{{ $message or 'Are you sure you would like to delete this entry?' }}');">
    				<span class="glyphicon glyphicon-trash"></span>
    		</button>
        {{ Form::close() }}
    	</td>
	@endunless
    	<td headers="description_col" rowspan="{{ count($person->line_items) + 1 }}">{{ $person->description }}</td>
    </tr>
	@foreach ($person->line_items as $item)
    <tr class="small {{ $item->rateclass->bgcolor or '' }}">
    	<td headers="rateclass_col" class="text-center">{!! $item->rateclass->getCodeAbbr() !!}</td>
    	<td headers="qty_col" class="text-center">{{ $item->qty }}</td>
    	<td headers="hours_col" class="text-right">{{ number_format($item->hours, 1) }}</td>
    	<td headers="payroll_col" class="text-right">{{ number_format($item->calcPayroll(), 1) }}</td>
    	<td headers="cost_col" class="text-right">
    		{{ number_format($item->cost, 2) }}
    		@if ($item->cost_overridden)
    		<span title="Cost Overridden" data-toggle="tooltip" class="glyphicon glyphicon-flash text-danger"></span>
        	@endif

            @if ($item->cost_secondrate)
            <span title="50% Rate" data-toggle="tooltip" class="glyphicon glyphicon-circle-arrow-down text-danger"></span>
            @endif
    	</td>
    	<td headers="amount_col" class="text-right">{{ number_format($item->calcAmount(), 2) }}</td>
    </tr>
    @endforeach
@endforeach
</tbody>
<tfoot>
	<tr>
        @unless ($readonly)
    		<td colspan="2">
    			<a href="{{ route('people.create', ['budget_id' => $budget->id, 'day_id' => isset($day) ? $day->id : '']) }}" class="btn btn-warning btn-xs">Add a New Person/Group</a>
    		</td>
        @endunless
		<td colspan="{{ $readonly ? 6 : 5 }}" class="text-right"><strong>TOTAL:</strong></td>
		<td headers="amount_col" class="text-right"><strong>{{ number_format(isset($day) ? $day->calcTotalAmount() : 0, 2) }}</strong></td>
</tr>
</tfoot>
</table>
