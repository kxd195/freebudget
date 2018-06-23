<div class="modal fade" id="sceneModal" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
<div class="modal-content">
	{{ Form::open(['route' => ['scenes.update', 'scene-info-here']]) }}
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">Modify Scene Details</h4>
	</div>
	<div class="modal-body">
		<div class="form form-horizontal">
			<div class="form-group">
				{{ Form::labelRequired('name', 'Scene Name:', ['class' => 'col-md-3 control-label']) }}
				<div class="col-md-9">
				{{ Form::text('name', '', ['size' => 50, 'class' => 'form-control', 'id' => 'scene-name']) }}
				@if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
				</div>
			</div>

			<div class="form-group">
				{{ Form::label('description', 'Description:', ['class' => 'col-md-3 control-label']) }}
				<div class="col-md-9">
				{{ Form::text('description', '', ['size' => 50, 'class' => 'form-control']) }}
				</div>
			</div>

			<div class="form-group">
				{{ Form::label('location', 'Location:', ['class' => 'col-md-3 control-label']) }}
				<div class="col-md-9">
				{{ Form::text('location', '', ['size' => 50, 'class' => 'form-control']) }}
				</div>
			</div>

			<div class="form-group">
				{{ Form::label('notes', 'Notes:', ['class' => 'col-md-3 control-label']) }}
				<div class="col-md-9">
				{{ Form::text('notes', '', ['size' => 50, 'class' => 'form-control']) }}
				</div>
			</div>

			<hr class="separator" />

			<div class="form-group">
				{{ Form::label('', 'Changes apply to:', ['class' => 'col-md-3 control-label']) }}
				<div class="col-md-9">
                    <div class="radio">
                        <label>
                            {{ Form::radio('appliesTo', 'all', true) }}
                            All days currently with this scene
                        </label>
                    </div>
					<div class="radio">
						<label>
							{{ Form::radio('appliesTo', 'day', false) }}
							This day only: <strong id="day-name" class="text-danger"></strong>
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		{{ method_field('PUT') }}
		{{ Form::hidden('budget_id', $budget->id) }}
		{{ Form::hidden('day_id') }}
		{{ Form::submit('Save Changes', ['class' => 'btn btn-primary']) }}
		<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	</div>
	{{ Form::close() }}
</div>
</div>
</div>