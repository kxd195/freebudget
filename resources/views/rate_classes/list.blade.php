@extends('layouts.app')

@section('content')
@component('components.messages')
@endcomponent

<div class="small">
	<a href="{{ route('home') }}"><strong>Home</strong></a>
	&gt; <a href="{{ route('settings') }}"><strong>System Settings</strong></a>
	&gt; Rate Classes
</div>

<div class="panel panel-primary">
    <div class="panel-heading"><h1 class="panel-title">Rate Classes</h1></div>
    <div class="panel-body">
    
    @if (sizeof($list) !== 0)
	<table class="table">
    <tr>
    		<th id="col_name">Name</th>
    		<th id="col_code" class="col-md-1 text-center">Code</th>
    		<th id="col_min_hours" class="col-md-2 text-center">Min. Hours</th>
    		<th id="col_rate" class="col-md-2 text-center">Rate</th>
    	</tr>
    	<tbody>
        	@foreach ($list as $item)
        		@if (!isset($last_category) || $item->category_id !== $last_category)
        			<tr class="active">
        				<th colspan="4"><strong>{{ $item->category->name }}</strong></th>
        			</tr>
        			<?php $last_category = $item->category_id; ?>
        		@endif
        		<tr>
        			<td headers="col_name">
        				<a href="{{ route('rate_classes.edit', $item->id) }}">{{ $item->name }}</a>
        			</td>
        			<td headers="col_code" class="text-center">{{ $item->code }}</td>
        			<td headers="col_min_hours" class="text-right">{{ $item->min_hours }}</td>
        			<td headers="col_rate" class="text-right">$ {{ $item->rate }}</td>
        		</tr>
        	@endforeach
    	</tbody>
    </table>
    @endif
    	
    <a class="btn btn-primary pull-right" href="{{ route('rate_classes.create') }}" role="button">Create a New Entry</a>
    </div>
</div>
@endsection
