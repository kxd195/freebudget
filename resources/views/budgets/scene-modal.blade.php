<div class="modal fade" id="sceneModal" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
<div class="modal-content">
	{{ Form::open(['route' => ['scene.edit', $budget->id]]) }}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Modify Scene</h4>
    </div>
    <div class="modal-body">
    		<div class="form form-horizontal">
        	<div class="form-group @if ($errors->has('day_id')) has-error @endif">
        		{{ Form::labelRequired('day_id', 'Day:', ['class' => 'col-md-3 control-label']) }}
        		<div class="col-md-9 form-inline">
        			{{ Form::select('day_id', $days, '', ['class' => 'form-control', 
        				'data-toggle' => 'tooltip', 'data-placement' => 'top',
        				'title' => 'Use this to move this scene to another day/date']) }}
    	    		@if ($errors->has('day_id')) <p class="help-block">{{ $errors->first('day_id') }}</p> @endif
        		</div>
        	</div>
    
        	<div class="form-group">
        		{{ Form::label('scene', 'Scene:', ['class' => 'col-md-3 control-label']) }}
        		<div class="col-md-9">
        		<div class="radio form-inline">
            		<label>
            			{{ Form::radio('scene_option', 'none', count($scenes) === 0 || !isset($entry->scene), ['onclick' => 'toggleScene(this);']) }}
            			Not associated to a scene
            		</label>
        		</div>
        		@unless (count($scenes) === 0)
            		<div class="radio form-inline">
                		<label>
                			{{ Form::radio('scene_option', 'select', true, ['onclick' => 'toggleScene(this);']) }}
                			Select existing:
                    		{{ Form::select('scene', $scenes, '', ['class' => 'form-control', 'placeholder' => 'Select...']) }}
                		</label>
            		</div>
        		@endunless
        		<div class="radio form-inline @if ($errors->has('scene_new')) has-error @endif">
            		<label>
            			{{ Form::radio('scene_option', 'new', false, ['onclick' => 'toggleScene(this);']) }}
            			Specify new:
	            		{{ Form::text('scene_new', '', ['size' => 40, 'class' => 'form-control', 'placeholder' => 'Enter scene number(s) and/or description']) }}
            		</label>
            		@if ($errors->has('scene_new')) <p class="help-block">{{ $errors->first('scene_new') }}</p> @endif
        		</div>
        		</div>
        	</div>
    		</div>
    </div>
    <div class="modal-footer">
    		{{ Form::hidden('old_day_id') }}
    		{{ Form::hidden('old_scene') }}
    		{{ Form::submit('Save Changes', ['class' => 'btn btn-primary']) }}
    		<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
    </div>
    	{{ Form::close() }}
</div>
</div>
</div>