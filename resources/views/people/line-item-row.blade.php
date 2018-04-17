@php
	$name_index = isset($counter) ? $counter : '';
	$id_index = isset($counter) ? '_' . $counter : '';
	$entry = isset($item) ? $item : new App\LineItem();
@endphp
<tr class="small {{ !isset($counter) ? 'hidden master-row' : '' }}" data-iteration="{{ $name_index }}">
<td headers="qty_col" class="@if ($errors->has('qty')) has-error @endif">
	{{ Form::number("line_items[{$name_index}][qty]", isset($entry->qty) ? $entry->qty : 1, ['class' => 'form-control text-right', 
		'id' => "qty{$id_index}", 'min' => 1, 'step' => 'any']) }}
	@if ($errors->has('qty')) <p class="help-block">{{ $errors->first('qty') }}</p> @endif
</td>
<td headers="rate_class_col" class="@if ($errors->has('rate_class_id')) has-error @endif">
	<select name="line_items[{{ $name_index }}][rate_class_id]" id="rate_class_id{{ $id_index }}" 
		class="form-control" onchange="recalculate(this);">
		<option disabled="disabled" hidden="hidden" {{ !isset($entry->rate_class_id) ? 'selected="selected"' : '' }}>Select...</option>
		@foreach ($categories as $category)
			<optgroup label="{{ $category->name }}">
			@foreach ($category->rate_classes as $rc)
				<option value="{{ $rc->id }}" {{ $rc->id == $entry->rate_class_id ? 'selected="selected"' : '' }}
					data-rate="{{ $rc->rate }}" data-hours="{{ $rc->min_hours }}" 
					data-code="{{ $rc->code }}" data-addon="{{ $rc->is_addon }}">
					{{ $rc->code }} - {{ $rc->name }}
				</option>
			@endforeach
			</optgroup>
		@endforeach
	</select>
	@if ($errors->has('rate_class_id')) <p class="help-block">{{ $errors->first('rate_class_id') }}</p> @endif
	<div class="checkbox quick-action-buttons" style="display:none">
		<a href="#" onclick="doQuickAction(this, 'RB');" class="btn btn-warning btn-xs has-tooltip"
			 data-toggle="tooltip" data-placement="bottom" 
			 title="Adds a Rehearsal/Blocking item with this entry">Add RB</a>

		<a href="#" onclick="doQuickAction(this, 'WC');" class="btn btn-warning btn-xs has-tooltip"
			 data-toggle="tooltip" data-placement="bottom" 
			 title="Adds a Wardrobe Change item with this entry">Add WC</a>	

		<a href="#" onclick="doQuickAction(this, 'WF');" class="btn btn-warning btn-xs has-tooltip"
			 data-toggle="tooltip" data-placement="bottom" 
			 title="Adds a Wardrobe Fitting item with this entry">Add WF</a>	
	</div>
</td>
<td headers="hours_col" class="@if ($errors->has('hours')) has-error @endif">
	{{ Form::number("line_items[{$name_index}][hours]", number_format($entry->hours, 1), ['class' => 'form-control text-right', 
		'id' => "hours{$id_index}", 'min' => ($entry->rateclass !== null ? $entry->rateclass->min_hours : 0), 
		'step' => 'any', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'data-default-title' => 'Minimum: ']) }}
	@if ($errors->has('hours')) <p class="help-block">{{ $errors->first('hours') }}</p> @endif
</td>
<td headers="payroll_col">
	{{ Form::text("line_items[{$name_index}][payroll]", number_format($entry->calcPayroll(), 1), ['class' => 'form-control text-right', 'readonly', 'id' => "payroll{$id_index}"]) }}
</td>
<td headers="cost_col" class="@if ($errors->has('cost')) has-error @endif">
	{{ Form::text("line_items[{$name_index}][cost]", number_format($entry->cost, 2), ['class' => 'form-control text-right', 
		'id' => "cost{$id_index}", 
		'readonly' => !$entry->cost_overridden]) }}
	{{ Form::hidden("line_items[{$name_index}][cost_original]", number_format($entry->cost_original, 2), ['id' => "cost_original{$id_index}"]) }}
	@if ($errors->has('cost')) <p class="help-block">{{ $errors->first('cost') }}</p> @endif
	<div class="checkbox col-sm-6">
		<label class="has-tooltip" data-toggle="tooltip" data-placement="bottom" title="Use this to manually override the suggested cost">
		{{ Form::hidden("line_items[{$name_index}][cost_overridden]", 0) }}
        	{{ Form::checkbox("line_items[{$name_index}][cost_overridden]", true, $entry->cost_overridden, ['onclick' => 'toggleCost(this);', 'id' => "cost_overridden{$id_index}"]) }}
        	Override
		</label>
	</div>
	<div class="checkbox col-sm-6">
		<label data-toggle="tooltip" data-placement="bottom" title="Use this to set this item as a 50% category">
		{{ Form::hidden("line_items[{$name_index}][cost_secondrate]", 0) }}
        	{{ Form::checkbox("line_items[{$name_index}][cost_secondrate]", true, $entry->cost_secondrate, ['onclick' => 'toggleCost(this);', 'id' => "cost_secondrate{$id_index}"]) }}
        	50%
		</label>
	</div>
</td>
<td headers="amount_col" class="text-right">
	{{ Form::text("line_items[{$name_index}][amount]", number_format($entry->calcAmount(), 2), 
		['class' => 'form-control text-right', 'readonly', 'id' => "amount{$id_index}"]) }}
	{{ Form::hidden("line_items[{$name_index}][id]", $entry->id) }}
</td>
<td headers="action_col" class="text-center">
	<a class="btn btn-danger" onclick="removeRow(this);">
		<span class="glyphicon glyphicon-trash"></span>
	</a>
</td>
</tr>
