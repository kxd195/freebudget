@extends('layouts.app')

@section('content')
@component('components.messages')
@endcomponent

<div>
	<a href="{{ route('home') }}"><strong>Home</strong></a>
	&gt; Current Productions
</div>

<div class="panel panel-primary">
<div class="panel-heading"><h1 class="panel-title">Current Productions</h1></div>

<div class="panel-body">
    <table class="table table-condensed">
        <thead>
            <tr>
                <th id="production_col">Production</th>
                <th id="budget_col">Budgets</th>
            </tr>
        </thead>
        <tbody>
        @foreach (App\Production::getTypes() as $type)
        @if (count($list[$type]) > 0)
            <tr class="bg-info">
                <td colspan="2"><strong class="h4">{{ $type }}</strong></td>
            </tr>
            @foreach ($list[$type] as $production)
                <tr>
                    <td headers="production_col">
                        <a href="{{ route('productions.edit', $production->id) }}"><strong>{{ $production->name }}</strong></a>
                    </td>
                    <td headers="budget_col">
                        <ul>
                        @foreach ($production->budgets->sortBy('episode') as $budget)
                            <li><a href="{{ route('budgets.show', $budget->id) }}">{{ isset($budget->episode) ? 'Ep. ' . $budget->episode . ' - ' : '' }} {{ $budget->name }}</a></li>
                        @endforeach
                        <li class="list-unstyled"><a href="{{ route('budgets.create', ['production_id' => $production->id]) }}" class="btn btn-primary btn-xs">Create a New Budget</a></li>
                        </ul>
                    </td>
                </tr>
            @endforeach
        @endif
        @endforeach
        </tbody>

    </table>
</div>
</div>
@endsection
