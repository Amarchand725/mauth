{!! Form::model($formModel, [
    'route' => '{modelName}.index',
    'method' => 'get'
]) !!}



{searchForm}


{!! Form::close() !!}