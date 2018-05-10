@extends('layouts.app')

@section('css')
<link href="{{ asset('css/jquery.scrolling-tabs.css') }}" rel="stylesheet" />
@endsection
@section('scripts')
<script type="text/javascript" src="{{ asset('js/clipboard.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.scrolling-tabs.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.matchHeight-min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/budgets.daytabs.js') }}"></script>
@endsection

@section('content')
@component('components.messages')
@endcomponent

<h1>{{ $budget->show->name }} - {{ $budget->name }}</h1>

<div class="row" id="budget_row">
<div class="col-md-9">
    <div class="panel panel-primary">
    <div class="panel-heading">
    	<h1 class="panel-title">{{ $budget->name }}
    	@unless ($from_share)
        	<button type="button" class="btn btn-info btn-xs pull-right" data-toggle="modal" data-target="#shareModal">
        		<span class="glyphicon glyphicon-share"></span>
        		Share
        	</button>
        @endunless
    	</h1>
    </div>
    
    <div class="panel-body form-horizontal">
    	<div class="row">
    		<label class="col-md-3 control-label">Show:</label>
    		<div class="col-md-9 form-control-static">{{ $budget->show->name }}</div>
    	</div>

    	<div class="row">
    		<label class="col-md-3 control-label">Type:</label>
    		<div class="col-md-3 form-control-static">{{ $budget->show->type }}</div>
    		
    		@if ($budget->show->type === App\Show::TYPE_SERIES || $budget->show->type === App\Show::FEATURE)
        		<label class="col-md-3 control-label">{{ $budget->show->type === App\Show::TYPE_SERIES ? 'Episodes' : 'Shoot Days' }}:</label>
        		<div class="col-md-3 form-control-static">{{ $budget->show->qty }}</div>
    		@endif
    	</div>
    
		<hr role="separator" />

		@isset ($budget->version_info)
        	<div class="row">
        		<label class="col-md-3 control-label">Tagged Version:</label>
        		<div class="col-md-9 form-control-static text-danger">
        			<strong>{{ $budget->version_info->name or '' }}</strong>
        			{{ $budget->version_info->created_at }}
        			<small>by {{ $budget->version_info->user->name }}</small>
        		</div>
        	</div>
		@endisset
		
    	<div class="row">
    		<label class="col-md-3 control-label">Start Date:</label>
    		<div class="col-md-3 form-control-static">{{ $budget->startdate !== null ? $budget->startdate->format('D, M j, Y') : "" }}</div>
    	</div>

		@isset ($budget->description)
        	<div class="row">
        		<label class="col-md-3 control-label">Description:</label>
        		<div class="col-md-9 form-control-static">{!! nl2br($budget->description) !!}</div>
        	</div>
		@endisset

		@isset ($budget->notes)
        	<div class="row">
        		<label class="col-md-3 control-label">Notes:</label>
        		<div class="col-md-9 form-control-static">{!! nl2br($budget->notes) !!}</div>
        	</div>
        @endisset
        
        @if (isset($budget->budget_versions) && count($budget->budget_versions) > 0 && !$readonly)
    		<div class="row">
    			<label class="col-md-3 control-label">Previous Versions:</label>
    			<div class="col-md-9 form-control-static">
    			<ul>
                    @foreach ($budget->budget_versions->reverse() as $version)
            		<li>
            			<a href="{{ route('budgets.version', [$budget->id, $version->id]) }}" target="_blank"><strong>{{ $version->name or '' }}</strong> {{ $version->created_at->setTimezone('Canada/Pacific') }}</a>
            			<small>by {{ $version->user->name }}</small>
            		</li>
                    @endforeach
    			</ul>
    			</div>
    		</div>
        @endif
        
        @unless ($readonly)
        	<div class="row">
        	<div class="col-md-12">
        	   <div class="pull-right form-inline">
                <a href="{{ route('days.create', ['budget_id' => $budget->id ]) }}" class="btn btn-primary btn-xs">Create a New Day</a>
                	<a href="{{ route('budgets.edit', $budget->id) }}" class="btn btn-primary btn-xs">Edit Budget Details</a>
                	<button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#tagVersionModal">Tag as Version</button>
            	</div>
        	</div>
        	</div>
            	
			@include('budgets.show-tagVersion')            	
    	@endunless
    </div>
	</div>
</div>
<div class="col-md-3">
    <div class="panel panel-primary">
    <div class="panel-heading">Grand Totals</div>
    <div class="panel-body form-horizontal small">
  	  	@foreach ($units as $unit)
			@php $amount = $unit->calcTotalAmount($budget->days) @endphp
		
    		@if ($amount != 0)
    	    	<div class="row">
            		<label class="col-xs-7 control-label">{{ $unit->name }}:</label>
            		<div class="col-xs-5 text-right form-control-static"><strong>{{ number_format($amount, 2) }}</strong></div>
                </div>
        	@endif
   	 	@endforeach
    	
    	<div class="row">
    		<label class="col-xs-7 control-label">Total:</label>
    		<div class="col-xs-5 text-right form-control-static text-danger"><strong>{{ number_format($budget->calcTotalAmount(), 2) }}</strong></div>
    	</div>

    	<hr role="separator" />
    	
    	<div class="row">
    		<label class="col-xs-7 control-label">Stand-In:</label>
    		<div class="col-xs-5 text-right form-control-static"><strong>{{ $budget->calcStandIn() }}</strong></div>
    	</div>
    	
	    <div class="row">
    		<label class="col-xs-7 control-label">General Extra:</label>
    		<div class="col-xs-5 text-right form-control-static"><strong>{{ $budget->calcGeneralExtra() }}</strong></div>
    	</div>

	    <div class="row">
    		<label class="col-xs-7 control-label">Background:</label>
    		<div class="col-xs-5 text-right form-control-static"><strong>{{ $budget->calcBackground() }}</strong></div>
    	</div>
    </div>
    </div>
</div>
</div>

<div class="row">
<div class="col-md-12">
</div>
</div>

@if (sizeof($budget->days) !== 0)
    @include('budgets.daytabs')
@endif

@include('budgets.show-share')
@include('budgets.scene-modal')
@endsection