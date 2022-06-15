@extends('admin.master')
@section('content')

 @include('{modelName}._search')
<div class='ajax_content'>
@foreach($models as $model)
{index}
    <hr>
@endforeach
{{ $models->appends($_GET)->links() }}
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>



 @include('ajax_pagination')
@endsection