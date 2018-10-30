@php
	$name_index = isset($counter) ? $counter : '';
	$id_index = isset($counter) ? '_' . $counter : '';
	$person = isset($person) ? $person : new App\Person();
	$wrangler_rate = isset($wrangler) ? $wrangler : 0;
@endphp
<tr class="{{ !isset($counter) ? 'hidden master-row' : '' }}" data-iteration="{{ $name_index }}">
<td headers="qty_col" class="@if ($errors->has('qty')) has-error @endif">
	{{ Form::text("people[{$name_index}][qty]", isset($person->qty) ? $person->qty : 1, ['class' => 'form-control text-right', 
		'id' => "qty{$id_index}"]) }}
	@if ($errors->has('qty')) <p class="help-block">{{ $errors->first('qty') }}</p> @endif
</td>
<td headers="description_col" class="@if ($errors->has('description')) has-error @endif">
	{{ Form::text("people[{$name_index}][description]", $person->description, ['class' => 'form-control', 
		'id' => "description{$id_index}"]) }}
	@if ($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
</td>
<td headers="rate_class_col" class="@if ($errors->has('rate_class_id')) has-error @endif">
	<select name="people[{{ $name_index }}][rate_class_id]" id="rate_class_id{{ $id_index }}" 
		class="form-control" onchange="recalculate(this);">
		<option disabled="disabled" hidden="hidden" {{ !isset($person->rate_class_id) ? 'selected="selected"' : '' }}>Select...</option>
		@php $showQuickActions = false; @endphp
		@foreach ($categories as $category)
			<optgroup label="{{ $category->name }}">
			@foreach ($category->rate_classes as $rc)
				<option value="{{ $rc->id }}" {{ $rc->id == $person->rate_class_id ? 'selected="selected"' : '' }}
					data-rate="{{ $rc->code === 'WR' ? $wrangler_rate : $rc->rate }}" 
					data-daily="{{ $rc->is_daily ? 'true' : 'false' }}"
					data-hours="{{ $rc->min_hours }}" 
					data-code="{{ $rc->code }}" 
					data-addon="{{ $rc->is_addon ? 'true' : 'false' }}">
					{{ $rc->code }} - {{ $rc->name }}
				</option>
				@php 
					if ($rc->id == $person->rate_class_id && !$rc->is_addon)
						$showQuickActions = true;
				@endphp
			@endforeach
			</optgroup>
		@endforeach
	</select>
	@if ($errors->has('rate_class_id')) <p class="help-block">{{ $errors->first('rate_class_id') }}</p> @endif
	<div class="checkbox quick-action-buttons" style="{{ !$showQuickActions ? 'display:none;' : '' }}">
		<a href="#" onclick="doQuickAction(this, 'V');" class="btn btn-warning btn-xs has-tooltip"
			 data-toggle="tooltip" data-placement="bottom" style="font-size:80%;"
			 title="Adds a Vehicle item with this entry">+Vehicle</a>

		<a href="#" onclick="doQuickAction(this, 'P');" class="btn btn-warning btn-xs has-tooltip"
			 data-toggle="tooltip" data-placement="bottom" style="font-size:80%;" 
			 title="Adds a Prop item with this entry">+Prop</a>

		<a href="#" onclick="doQuickAction(this, 'D');" class="btn btn-warning btn-xs has-tooltip"
			 data-toggle="tooltip" data-placement="bottom" style="font-size:80%;" 
			 title="Adds a Dog item with this entry">+Dog</a>

		<a href="#" onclick="doQuickAction(this, 'WF');" class="btn btn-warning btn-xs has-tooltip"
			 data-toggle="tooltip" data-placement="bottom" style="font-size:80%;" 
			 title="Adds a Wardrobe Fitting item with this entry">+WF</a>	

		<a href="#" onclick="doQuickAction(this, 'WC');" class="btn btn-warning btn-xs has-tooltip"
			 data-toggle="tooltip" data-placement="bottom" style="font-size:80%;" 
			 title="Adds a Wardrobe Change item with this entry">+WC</a>	

		<a href="#" onclick="doQuickAction(this, 'C');" class="btn btn-warning btn-xs has-tooltip"
			 data-toggle="tooltip" data-placement="bottom" style="font-size:80%;" 
			 title="Adds a Costume item with this entry">+Costume</a>	
	</div>
</td>
<td headers="hours_col" class="@if ($errors->has('hours')) has-error @endif">
	{{ Form::text("people[{$name_index}][hours]", number_format($person->hours > 0 ? $person->hours : 8, 1), ['class' => 'form-control text-right', 
		'id' => "hours{$id_index}", 'min' => ($person->rateclass !== null ? $person->rateclass->min_hours : 0), 
		'step' => 'any', 'data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'data-default-title' => 'Minimum: ']) }}
	@if ($errors->has('hours')) <p class="help-block">{{ $errors->first('hours') }}</p> @endif
</td>
<td headers="payroll_col">
	{{ Form::text("people[{$name_index}][payroll]", number_format($person->calcPayroll(), 1), ['class' => 'form-control text-right', 'readonly', 'id' => "payroll{$id_index}"]) }}
</td>
<td headers="cost_col" class="@if ($errors->has('cost')) has-error @endif">
	{{ Form::text("people[{$name_index}][cost]", number_format($person->cost, 2), ['class' => 'form-control text-right', 
		'id' => "cost{$id_index}", 
		'readonly' => !$person->cost_overridden]) }}
	{{ Form::hidden("people[{$name_index}][cost_original]", number_format($person->cost_original, 2), ['id' => "cost_original{$id_index}"]) }}
	@if ($errors->has('cost')) <p class="help-block">{{ $errors->first('cost') }}</p> @endif
	<div class="checkbox col-sm-6">
		<label class="has-tooltip" data-toggle="tooltip" data-placement="bottom" title="Use this to manually override the suggested cost">
		{{ Form::hidden("people[{$name_index}][cost_overridden]", 0) }}
        	{{ Form::checkbox("people[{$name_index}][cost_overridden]", true, $person->cost_overridden, ['onclick' => 'toggleCost(this);', 'id' => "cost_overridden{$id_index}"]) }}
        	Override
		</label>
	</div>
	<div class="checkbox col-sm-6">
		<label data-toggle="tooltip" data-placement="bottom" title="Use this to set this item as a 50% category">
		{{ Form::hidden("people[{$name_index}][cost_secondrate]", 0) }}
        	{{ Form::checkbox("people[{$name_index}][cost_secondrate]", true, $person->cost_secondrate, ['onclick' => 'toggleCost(this);', 'id' => "cost_secondrate{$id_index}"]) }}
        	50%
		</label>
	</div>
</td>
<td headers="amount_col" class="text-right">
	{{ Form::text("people[{$name_index}][amount]", number_format($person->calcAmount(), 2), 
		['class' => 'form-control text-right', 'readonly', 'id' => "amount{$id_index}"]) }}
	{{ Form::hidden("people[{$name_index}][id]", $person->id) }}
	<div class="checkbox">
		<a class="btn btn-danger btn-xs" onclick="removeRow(this);">
			Delete Person
		</a>
	</div>
</td>
</tr>
