@if(Session::has('flash_message'))
    <div class="alert alert-success">
        {{ Session::get('flash_message') }}
    </div>
@endif
@if(Session::has('record_exists'))
    <div class="alert alert-danger">
        {{ Session::get('record_exists') }}
    </div>
@endif

