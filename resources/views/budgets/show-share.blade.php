<div class="modal fade" id="shareModal" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
<div class="modal-content">
    	{{ Form::open(['route' => ['shares.store'], 'class' => 'form-horizontal']) }}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Share</h4>
    </div>
    <div class="modal-body">
   		<p>Create a hyperlink to your budget that you can share with others.</p>
   		
   		<div class="form-group">
   			{{ Form::label('modifiable', 'Access:', ['class' => 'col-md-4 control-label']) }}
   			<div class="col-md-8">
   			<div class="radio">
       			<label>
       				{{ Form::radio('modifiable', 0, true) }}
       				Read-only
       			</label>
   			</div>
   			@unless ($readonly)
   			<div class="radio">
       			<label>
       				{{ Form::radio('modifiable', 1) }}
       				Read &amp; Write
       			</label>
       		</div>
       		@endunless
   			</div>
   		</div>

   		<div class="form-group">
   			{{ Form::label('expires_after', 'Expire After:', ['class' => 'col-md-4 control-label']) }}
   			<div class="col-md-4">
   			<div class="input-group">
	   			{{ Form::number('expires_after', 0, ['class' => 'form-control', 'min' => 0]) }}
   				<span class="input-group-addon">days</span>
   			</div>
   			<p class="help-block small">Use "0 days" for indefinite</p>
   			</div>
   		</div>
    </div>
    <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		{{ Form::hidden('budget_id', $budget->id) }}
		@isset ($budget->version_info->id)
			{{ Form::hidden('budget_version_id', $budget->version_info->id) }}
		@endisset
		{{ Form::button('Create Share', ['class' => 'btn btn-primary', 'role' => 'submit', 'id' => 'shareCreateButton']) }}            	
    </div>
    	{{ Form::close() }}
</div>
</div>
</div>

<div class="modal fade" id="shareCreatedModal" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-success">Share successfully created!</h4>
    </div>
    <div class="modal-body form">
   		<div class="form-group">
   			{{ Form::label('hyperlink', 'Here is your shareable hyperlink:', ['class' => 'control-label']) }}
   			<div class="input-group">
        			{{ Form::text('hyperlink', $value = null, ['class' => 'form-control', 'id' => 'shareHyperlink', 'readonly']) }}
        			<span class="input-group-btn">
                <button class="btn btn-primary" id="hyperlinkCopyButton" data-clipboard-target="#shareHyperlink">
                    <span class="glyphicon glyphicon-copy"></span>
                    <span class="copy-text">Copy&nbsp;&nbsp;&nbsp;&nbsp;</span>
                </button>
                </span>
   			</div>
   		</div>

   		<div class="form-horizontal">
   		<div class="form-group">
   			{{ Form::label('access', 'Access:', ['class' => 'col-md-4 control-label']) }}
   			<div class="col-md-8">
			{{ Form::text('access', $value = null, ['class' => 'form-control', 'id' => 'shareAccess', 'readonly']) }}
   			</div>
   		</div>

   		<div class="form-group">
   			{{ Form::label('expires_at', 'It will expire on:', ['class' => 'col-md-4 control-label']) }}
   			<div class="col-md-8">
			{{ Form::text('expires_at', $value = null, ['class' => 'form-control', 'id' => 'shareExpiresAt', 'readonly']) }}
   			</div>
   		</div>
   		</div>
    </div>
    <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
</div>
</div>
</div>

