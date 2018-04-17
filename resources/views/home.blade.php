@extends('layouts.app')

@section('content')
@component('components.messages')
@endcomponent

<div class="small">
	Home
</div>
<div class="panel panel-primary">
    <div class="panel-heading"><h1 class="panel-title">Home</h1></div>

    <div class="panel-body">
        <strong>Recently Updated Shows</strong>
        <ul>
        @foreach ($list as $show)
        		<li>
            		<a href="{{ route('shows.edit', $show->id) }}">{{ $show->name }}</a>
        			<ul>
        			@foreach ($show->budgets as $budget)
        				<li><a href="{{ route('budgets.show', $budget->id) }}">{{ $budget->name }}</a></li>
        			@endforeach
    				<li><a href="{{ route('budgets.create', ['show_id' => $show->id]) }}"><em>Create a New Budget</em></a></li>
        			</ul>
        		</li>
        @endforeach
        </ul>

        	<a href="{{ route('shows.create') }}" class="btn btn-primary pull-right">Create a New Show</a>
    </div>
</div>
@endsection
