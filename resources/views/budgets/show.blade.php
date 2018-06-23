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

@if ($readonly)
	<div class="alert alert-danger" role="alert">
		<strong>IMPORTANT:</strong> You are currently viewing a read-only version/snapshot of this budget. 
	</div>
@endif

<div class="small">
	<a href="{{ route('home') }}"><strong>Home</strong></a>
	&gt; <a href="{{ route('productions.edit', $budget->production_id) }}"><strong>{{ $budget->production->name }}</strong></a>
	&gt; {{ $budget->name }}
</div>

<div class="row" id="budget_row">
<div class="col-md-10">
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
			<label class="col-md-2">Production Name:</label>
			<div class="col-md-4">{{ $budget->production->name }}</div>
			<label class="col-md-2">Type:</label>
			<div class="col-md-4">{{ $budget->production->type }}</div>
		</div>

		<div class="row">
			<label class="col-md-2">Season:</label>
			<div class="col-md-4">{{ $budget->production->season }}</div>
			@if ($budget->production->type === App\Production::TYPE_SERIES || $budget->production->type === App\Production::FEATURE)
				<label class="col-md-2">{{ $budget->production->type === App\Production::TYPE_SERIES ? '# of Episodes' : '# of Shoot Days' }}:</label>
				<div class="col-md-4">{{ $budget->production->qty }}</div>
			@endif
		</div>

		<div class="row">
			<label class="col-md-2">Episode:</label>
			<div class="col-md-4">{{ $budget->episode }}</div>
		</div>
	
		@isset ($budget->version_info)
			<div class="row">
				<label class="col-md-2">Tagged Version:</label>
				<div class="col-md-10 text-danger">
					<strong>{{ $budget->version_info->name or '' }}</strong>
					{{ $budget->version_info->created_at }}
					<small>by {{ $budget->version_info->user->name }}</small>
				</div>
			</div>
		@endisset
		
		<div class="row">
			<label class="col-md-2">Start Date:</label>
			<div class="col-md-4">{{ $budget->startdate !== null ? $budget->startdate->format('D, M j, Y') : "" }}</div>
			<label class="col-md-2">{{ $budget->enddate !== null ? 'End Date' : '# of Shoot Days' }}:</label>
			<div class="col-md-4">{{ $budget->enddate !== null ? $budget->enddate->format('D, M j, Y') : $budget->num_days }}</div>
		</div>

		@isset ($budget->description)
			<div class="row">
				<label class="col-md-2">Description:</label>
				<div class="col-md-10">{!! nl2br($budget->description) !!}</div>
			</div>
		@endisset

		@isset ($budget->notes)
			<div class="row">
				<label class="col-md-2">Notes:</label>
				<div class="col-md-10">{!! nl2br($budget->notes) !!}</div>
			</div>
		@endisset
		
		@if (isset($budget->budget_versions) && count($budget->budget_versions) > 0 && !$readonly)
			<div class="row">
				<label class="col-md-2">Previous Versions:</label>
				<div class="col-md-10">
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
	</div>
	</div>
	
	@if (sizeof($budget->days) !== 0)
		@include('budgets.daytabs-table')
	@endif
</div>
<div class="col-md-2">
	<div class="panel panel-primary">
	<div class="panel-heading">Grand Totals</div>
	<div class="panel-body form-horizontal small">
  	  	@foreach ($units as $unit)
			@php $amount = $unit->calcTotalAmount($budget->days) @endphp
		
			@if ($amount != 0)
				<div class="row">
					<label class="col-xs-7">{{ $unit->name }}:</label>
					<div class="col-xs-5 text-right"><strong>{{ number_format($amount, 2) }}</strong></div>
				</div>
			@endif
   	 	@endforeach
		
		<div class="row">
			<label class="col-xs-7">Total:</label>
			<div class="col-xs-5 text-right text-danger"><strong>{{ number_format($budget->calcTotalAmount(), 2) }}</strong></div>
		</div>

		<hr role="separator" />
		
		<div class="row">
			<label class="col-xs-7">Stand-In:</label>
			<div class="col-xs-5 text-right"><strong>{{ $budget->calcStandIn() }}</strong></div>
		</div>
		
		<div class="row">
			<label class="col-xs-7">General Extra:</label>
			<div class="col-xs-5 text-right"><strong>{{ $budget->calcGeneralExtra() }}</strong></div>
		</div>

		<div class="row">
			<label class="col-xs-7">Background:</label>
			<div class="col-xs-5 text-right"><strong>{{ $budget->calcBackground() }}</strong></div>
		</div>
	</div>
	</div>

	@unless ($readonly)
		<div class="form-group">
		<button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#tagVersionModal">Tag Budget Version</button>
		<a href="{{ route('budgets.edit', $budget->id) }}" class="btn btn-info btn-block">Modify Budget Details</a>
		<a href="{{ route('days.create', ['budget_id' => $budget->id ]) }}" class="btn btn-info btn-block">Create a New Day</a>
		@include('budgets.show-tagVersion')
		</div>
	@endunless

	<div class="form-group">
	<select id="day-quickjump" name="day-quickjump" class="form-control" onchange="dayQuickjump()">
		<option value="ALL">Show All Days</option>
		@foreach ($budget->days as $day)
				<option value="{{ $day->id }}">{{ $day->generateName() }}</option>
		@endforeach
	</select>
	</div>

	@unless ($readonly)
	<a href="{{ route('people.create', ['budget_id' => $budget->id]) }}" class="btn btn-info btn-block">Add Entries</a>
	@endunless

</div>
</div>
@include('budgets.show-share')
@include('budgets.scene-modal')
@endsection