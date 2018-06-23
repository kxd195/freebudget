<div class="modal fade" id="tagVersionModal" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
<div class="modal-content">
    	{{ Form::open(['route' => ['budgets.tag', $budget->id]]) }}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Tag as Version</h4>
    </div>
    <div class="modal-body">
      <div class="form form-horizontal">
   		<p>Tagging as version captures a read-only snapshot of your current budget.</p>
   		
   		<div class="form-group">
   			{{ Form::label('name', 'Revision Name:', ['class' => 'col-md-4 control-label']) }}
   			<div class="col-md-8">
   			{{ Form::text('name', $value = null, ['class' => 'form-control']) }}
   			</div>
   		</div>
    </div>
    </div>
    <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		{{ Form::submit('Tag as Version', ['class' => 'btn btn-warning']) }}            	
    </div>
    	{{ Form::close() }}
</div>
</div>
</div>
