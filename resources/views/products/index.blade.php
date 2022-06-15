@extends('layouts.admin.app')
@section('title', 'PRODUCTS')
@section('content')
<input type="hidden" id="page_url" value="{{ route('product.index') }}">
<section class="content-header">
    <div class="content-header-left">
        <h1>ALL PRODUCTS</h1>
    </div>
    <div class="content-header-right">
        <a href="{{ route('product.create') }}" class="btn btn-primary btn-sm">ADD NEW PRODUCT</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            @if (session('success'))
                <div class="callout callout-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="box box-info">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-1">Search:</div>
                        <div class="d-flex col-sm-6">
                            <input type="text" id="search" class="form-control" placeholder="Search" style="margin-bottom:5px">
                        </div>
                    </div>
                    <table id="" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>NAME</th><th>DESCRIPTION</th><th>STATUS</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="body">
                            @foreach($models as $key=>$model)
                                <tr id="id-{{ $model->id }}">
                                    <td>{{  $models->firstItem()+$key }}.</td>
                                    <td>{{ $model->name }}</td><td>{{ $model->description }}</td><td>{{ $model->status }}</td><hr>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="6">
                                    Displying {{$models->firstItem()}} to {{$models->lastItem()}} of {{$models->total()}} records
                                    <div class="d-flex justify-content-center">
                                        {!! $models->links('pagination::bootstrap-4') !!}
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</section>
@endsection
@push('js')
@endpush
