@if (session('success'))
    <div class="alert alert-success" role="alert">
        <button type="button" class="close" data-dismiss="alert">×</button>
        {{ session('success') }}
    </div>
@elseif(session('error'))
    <div class="alert alert-danger" role="alert">
        <button type="button" class="close" data-dismiss="alert">×</button>
        {{ session('error') }}
    </div>

@elseif(session('info'))
    <div class="alert alert-info" role="alert">
        <button type="button" class="close" data-dismiss="alert">×</button>
        {{ session('info') }}
    </div>
@endif
