@foreach($models as $key=>$model)
    <tr id="id-{{ $model->id }}">
        {index}
    </tr>
@endforeach