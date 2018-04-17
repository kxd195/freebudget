@if (session('message'))
    <div class="alert alert-{{ session('message-class') ? session('message-class') : 'success' }} alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ session('message') }}
    </div>
@endif            
@if (session('message-warning'))
    <div class="alert alert-{{ session('message-class') ? session('message-class') : 'warning' }} alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ session('message-warning') }}
    </div>
@endif            
@if (session('message-danger'))
    <div class="alert alert-{{ session('message-class') ? session('message-class') : 'danger' }} alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ session('message-danger') }}
    </div>
@endif            
