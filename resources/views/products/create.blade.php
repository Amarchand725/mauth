@extends('layouts.admin.app')
@section('title', "ADD NEW Product")
@push('css')
    <style>
        select {
            font-family: 'Font Awesome', 'sans-serif';
        }
    </style>
@endpush
@section('content')
<section class="content-header">
	<div class="content-header-left">
		<h1>ADD NEW Product</h1>
	</div>
	<div class="content-header-right">
		<a href="{{ route("product.index") }}" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<form action="{{ route("product.store") }}" id="regform" class="form-horizontal" enctype="multipart/form-data" method="post" accept-charset="utf-8">
				@csrf
				<div class="box box-info">
					<div class="box-body">
                        <div class="form-group">
<label for="" class="col-sm-2 control-label">Name <span style="color:red">*</span></label>
<div class="col-sm-8"><input type="text" class="form-control" name="name" value="" placeholder="Enter name">
<span style="color: red">{{ $errors->first("name") }}</span></div></div>
<div class="form-group">
<label for="" class="col-sm-2 control-label">Description <span style="color:red">*</span></label>
<div class="col-sm-8"><input type="text" class="form-control" name="description" value="" placeholder="Enter description">
<span style="color: red">{{ $errors->first("description") }}</span></div></div>
<div class="form-group">
<label for="" class="col-sm-2 control-label">Status <span style="color:red">*</span></label>
<div class="col-sm-8"><input type="text" class="form-control" name="status" value="" placeholder="Enter status">
<span style="color: red">{{ $errors->first("status") }}</span></div></div>
<label for="" class="col-sm-2 control-label"></label>
<div class="col-sm-6"><button type="submit" class="btn btn-success pull-left">Save</button></div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>

@endsection
@push('js')
@endpush
