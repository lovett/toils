<div class="container text-right">
    {!! Form::model($model, ['route' => [$route, $model->id], 'method' => 'DELETE']) !!}
    {!! Form::button($label, ['type' => 'submit', 'class' => 'btn btn-sm btn-danger']) !!}
    {!! Form::close() !!}
</div>
