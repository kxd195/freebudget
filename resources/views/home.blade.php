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
        <a href="{{ route('productions.create') }}" class="btn btn-primary pull-right">Create New Production</a>
        <strong>Current Productions</strong>
        <ul>
        @foreach ($list as $production)
        		<li>
            		<a href="{{ route('productions.edit', $production->id) }}">{{ $production->name }}</a>
        			<ul>
        			@foreach ($production->budgets as $budget)
        				<li><a href="{{ route('budgets.show', $budget->id) }}">{{ $budget->name }}</a></li>
        			@endforeach
    				<li><a href="{{ route('budgets.create', ['production_id' => $production->id]) }}"><em>Create a New Budget</em></a></li>
        			</ul>
        		</li>
        @endforeach
        </ul>

    </div>

    <div class="panel-body">
        <strong>Completed Productions</strong>
        <ul>

        </ul>
    </div>
</div>
@endsection
