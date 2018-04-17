@extends('layouts.app')

@section('content')
@component('components.messages')
@endcomponent

<div class="small">
	<a href="{{ route('home') }}"><strong>Home</strong></a>
	&gt; Shows
</div>

<div class="panel panel-primary">
    <div class="panel-heading"><h1 class="panel-title">Shows</h1></div>
    <div class="panel-body">
    
    <ul>
    	@foreach ($list as $item)
    		<li>
    			<a href="{{ route('shows.edit', $item->id) }}">{{ $item->name }}</a>
    			<ul>
    				@foreach ($item->budgets as $budget)
    					<li><a href="{{ route('budgets.show', $budget->id) }}">{{ $budget->name }}</a></li>
    				@endforeach
    				<li><a href="{{ route('budgets.create', ['show_id' => $item->id]) }}"><em>Create a New Budget</em></a></li>
    			</ul>
    		</li>
    	@endforeach
    	
    	@if (sizeof($list) === 0)
    		<li><em class="text-danger">No shows currently exist!</em></li>
    	@endif
    </ul>
    
    
    <a class="btn btn-primary pull-right" href="{{ route('shows.create') }}" role="button">Create a New Show</a>
    </div>
</div>
@endsection
