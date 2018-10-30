@extends('layouts.app')

@section('content')
@component('components.messages')
@endcomponent

<div>
	<a href="{{ route('home') }}"><strong>Home</strong></a>
	&gt; <a href="{{ route('settings') }}"><strong>System Settings</strong></a>
	&gt; Units
</div>

<div class="panel panel-primary">
    <div class="panel-heading"><h1 class="panel-title">Units</h1></div>
    <div class="panel-body">
    
    <ul>
    	@foreach ($list as $item)
    		<li><a href="{{ route('units.edit', $item->id) }}">{{ $item->name }}</a></li>
    	@endforeach
    	
    	@if (sizeof($list) === 0)
    		<li><em class="text-danger">No entries currently exist!</em></li>
    	@endif
    </ul>
    
    
    <a class="btn btn-primary pull-right" href="{{ route('units.create') }}" role="button">Create a New Entry</a>
    </div>
</div>
@endsection
