<div class="modal fade" id="dayModal" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Move/copy to multiple days</h4>
    </div>
    <div class="modal-body">
    		<p class="text-success text-center"><strong>**</strong> indicates currently selected day.</p>
    		<div class="form form-horizontal">
        	<div class="form-group @if ($errors->has('day_id')) has-error @endif">
        		{{ Form::labelRequired('day_id', 'Day:', ['class' => 'col-md-2 control-label']) }}
        		<div class="col-md-10">
        			<div class="col-md-12 checkbox">
        			<label class="text-danger">
        				{{ Form::checkbox('multiple_day_select_all', 1, false, ['onclick' => 'selectAllDays()']) }}
        				Select all
        			</label>
        			</div>
    
            		@foreach ($days->keys() as $day_id)
                		<div class="col-md-6 checkbox {{ $day_id === $entry->day_id || (empty($day_id) && empty($entry->day_id)) ? 'text-success' : '' }}">
                		<label>
                			{{ Form::checkbox('multiple_day_id', $day_id, $day_id === $entry->day_id || (empty($day_id) && empty($entry->day_id))) }}
                			{{ $days->get($day_id) }}{{ $day_id === $entry->day_id || (empty($day_id) && empty($entry->day_id)) ? '**' : '' }}
                		</label>
                		</div>
            		@endforeach
        		</div>
        	</div>
    		</div>
    </div>
    <div class="modal-footer">
		<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="copyToMultipleDays()">Apply</button>
    </div>
</div>
</div>
</div>