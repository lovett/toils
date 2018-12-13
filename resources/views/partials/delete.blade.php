<div class="container d-flex justify-content-end mb-4">
    {!! Form::model($model, ['route' => [$route, $model->id], 'method' => 'DELETE']) !!}
    {!! Form::button($label, ['type' => 'submit', 'class' => 'btn btn-sm btn-danger']) !!}
    {!! Form::close() !!}
</div>
