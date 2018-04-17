@extends('layouts.app')

@section('content')
<div class="small">
	<a href="{{ route('home') }}"><strong>Home</strong></a>
	&gt; System Settings
</div>
<div class="panel panel-primary">
    <div class="panel-heading"><h1 class="panel-title">System Settings</h1></div>
    <div class="panel-body">
    <ul>
    		<li><a href="{{ route('global_settings.edit', 1) }}">Global Settings</a></li>
    		<li><a href="{{ route('categories.index') }}">Rate Class Categories</a></li>
    		<li><a href="{{ route('rate_classes.index') }}">Rate Classes</a></li>
    		<li><a href="{{ route('units.index') }}">Units</a></li>
    </ul>
    </div>
</div>
@endsection
