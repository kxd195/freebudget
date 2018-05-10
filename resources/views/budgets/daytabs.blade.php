@php
    $show_day_id = intval(session('show_day_id') !== null ? session('show_day_id') : request('show_day_id'));
@endphp

<div class="row">
<div class="col-md-3 col-md-offset-9">
    <select id="day-quickjump" name="day-quickjump" class="form-control" onchange="dayQuickjump()">
        <option disabled="disabled" hidden="hidden" selected="selected">Jump to another day...</option>
        @foreach ($budget->days as $day)
        		<option value="#day{{ $day->id }}">{{ $day->generateName() }}</option>
        @endforeach
    </select>
</div>
</div>

<div class="row">
<div class="col-md-12">

<ul class="nav nav-tabs small" role="tablist">
@php $curr_day = 0; @endphp
    <li role="presentation" class="{{ 0 === $show_day_id ? 'active' : '' }}">
        <a href="#day0" aria-controls="day0" role="tab" 
            data-toggle="tab" class="small" 
            title="Undated entries">Undated Entries</a>
    </li>
    @foreach ($budget->days as $day)
        <li role="presentation" class="{{ $day->id === $show_day_id ? 'active' : '' }}">
    		<a href="#day{{ $day->id }}" aria-controls="day{{ $day->id }}" role="tab" 
    			data-toggle="tab" data-actualdate="{{ $day->actualdate->format('Y-m-d') }}" class="small" 
    			title="{{ $day->actualdate->format('D, M j, Y') }}">Day {{ $day->name }}</a>
    	</li>
    	@php $curr_day++ @endphp
    @endforeach
</ul>

<!-- Tab panes -->
<div class="tab-content">
@php $curr_day = 0; @endphp
    <div role="tabpanel" class="tab-pane {{ 0 === $show_day_id ? 'active' : '' }}" id="day0">
        <div class="row row-day-info">
        <div class="col-md-9">
            <div class="panel panel-info">
            <div class="panel-heading"><h2 class="panel-title">Undated Entries</h2></div>

            </div>
        </div>

        <div class="col-md-3">
            <div class="panel panel-info">
            <div class="panel-heading">Day Totals</div>
            <div class="panel-body form-horizontal small">
                @foreach ($units as $unit)
                    @php $amount = isset($day) ? $unit->calcTotalAmount($day->id) : 0; @endphp
                
                    @if ($amount != 0)
                        <div class="row">
                        <label class="col-xs-7 control-label">{{ $unit->name }}:</label>
                        <div class="col-xs-5 text-right form-control-static"><strong>{{ number_format($amount, 2) }}</strong></div>
                    </div>
                    @endif
                @endforeach
                
                <div class="row">
                    <label class="col-xs-7 control-label">Total:</label>
                    <div class="col-xs-5 text-right form-control-static text-danger"><strong>{{ number_format(isset($day) ? $day->calcTotalAmount() : 0, 2) }}</strong></div>
                </div>
            </div>
            </div>
        </div>
        </div>

        @include('budgets.daytabs-table', ['people_entries' => $budget->undated_entries])
    </div>

@foreach ($budget->days as $day)
	@php
		$last_unit = null;
		$last_location = null;
	@endphp

    <div role="tabpanel" class="tab-pane {{ $day->id === $show_day_id ? 'active' : '' }}" id="day{{ $day->id }}">
		<div class="row row-day-info">
		<div class="col-md-9">
			<div class="panel panel-info">
			<div class="panel-heading"><h2 class="panel-title">{{ $day->generateName() }}</h2></div>

    		<div class="panel-body form-horizontal">
        		<div class="row">
            		<label class="col-md-3 control-label">Location:</label>
            		<div class="col-md-9 form-control-static">{{ $day->location }}</div>
        		</div>

        		@isset ($day->crew_call)
        		<div class="row">
            		<label class="col-md-3 control-label">Estimated Crew Call:</label>
            		<div class="col-md-9 form-control-static">{{ $day->crew_call }}</div>
        		</div>
			@endisset
			
        		@isset ($day->notes)
            		<div class="row">
                		<label class="col-md-3 control-label">Notes:</label>
                		<div class="col-md-9 form-control-static">{!! nl2br($day->notes) !!}</div>
                </div>
            @endisset
         
         	@unless ($readonly)   
        		<div class="row">
            		<div class="col-md-12">
                {{ Form::model($day, [ 'route' => ['days.destroy', $day->id], 'class' => 'pull-right']) }}
					<a href="{{ route('days.edit', $day->id) }}" class="btn btn-primary btn-xs">Modify Day</a>

					{{ method_field('DELETE') }}
					{{ Form::submit('Delete Day', ['class' => 'btn btn-xs btn-danger',
                        	'onclick' => 'return confirm("Are you sure you would like to delete this day?\n\nWARNING: Deleting this day will remove all scenes/people within it!");']) }}
				{{ Form::close() }}
            		</div>
        		</div>
            @endunless
			</div>
		</div>
    	</div>
    
        <div class="col-md-3">
            <div class="panel panel-info">
            <div class="panel-heading">Day Totals</div>
            <div class="panel-body form-horizontal small">
          	  	@foreach ($units as $unit)
            			@php $amount = $unit->calcTotalAmount($day->id) @endphp
            		
                		@if ($amount != 0)
                	    	<div class="row">
                    		<label class="col-xs-7 control-label">{{ $unit->name }}:</label>
                    		<div class="col-xs-5 text-right form-control-static"><strong>{{ number_format($amount, 2) }}</strong></div>
                    	</div>
                    	@endif
           	 	@endforeach
            	
            	<div class="row">
            		<label class="col-xs-7 control-label">Total:</label>
            		<div class="col-xs-5 text-right form-control-static text-danger"><strong>{{ number_format($day->calcTotalAmount(), 2) }}</strong></div>
            	</div>
            	
                <!--

            	<hr role="separator" />
            	
        	    <div class="row">
            		<label class="col-xs-7 control-label">Stand-In:</label>
            		<div class="col-xs-5 text-right form-control-static"><strong>{{ $day->calcStandIn() }}</strong></div>
            	</div>

        	    <div class="row">
            		<label class="col-xs-7 control-label">General Extra:</label>
            		<div class="col-xs-5 text-right form-control-static"><strong>{{ $day->calcGeneralExtra() }}</strong></div>
            	</div>

        	    <div class="row">
            		<label class="col-xs-7 control-label">Background:</label>
            		<div class="col-xs-5 text-right form-control-static"><strong>{{ $day->calcBackground() }}</strong></div>
            	</div>

                -->
            </div>
            </div>
        </div>
        </div>
		@include('budgets.daytabs-table', ['people_entries' => $day->people])        
    </div>

    @php $curr_day++ @endphp
@endforeach
</div>

</div>
</div>

