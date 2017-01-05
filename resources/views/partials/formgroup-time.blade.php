<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    @if ($label)
        {!! Form::label($name, $label, ['class' => 'col-sm-2 control-label']) !!}
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'col-sm-offset-2' }}">
        <pickable inline-template format="hh:mm AA" initial="{{ date('h:i A') }}">
            {!! Form::text($name . $suffix, isset($model->$name)? $model->$name->format('g:i A') : '', ['class' => 'form-control', 'v-model' => 'pickResult']) !!}

            <p>
                <a href="#" @click.prevent="toggle($event)">
                    <span v-bind:class="{hidden: !isToggled}">hide</span> shortcuts
                </a>
            </p>

            <div class="shortcuts" v-bind:class="{toggled: isToggled}">
                <div class="well">
                    <p>
                        <span class="label">Hour:</span>
                        @foreach ($ranges['hour'] as $value)
                            <a @click.prevent="pick('hh', '{{ $value->format('g') }}', 'active', $event)"
                               href="#"
                               class="{{ isset($model->$name) && $value->format('g') === $model->$name->format('g') ? 'active' : '' }}"
                            >{{ $value->format('g') }}</a>
                        @endforeach
                    </p>
                    <p>
                        <span class="label">Minute:</span>
                        @foreach ($ranges['minute'] as $value)
                            <a @click.prevent="pick('mm', '{{ $value->format('i') }}', 'active', $event)"
                               href="#"
                               class="{{ isset($model->$name) && $value->format('i') === $model->$name->format('i') ? 'active' : '' }}"
                            >{{ $value->format('i') }}</a>
                        @endforeach
                    </p>
                    <p>
                        <a @click.prevent="pick('AA', 'AM', 'active', $event)"
                           href="#"
                           class="{{ isset($model->$name) && $value->format('A') === 'AM' ? 'active' : '' }}"
                        >AM</a>
                        <a @click.prevent="pick('AA', 'PM', 'active', $event)"
                           href="#"
                           class="{{ isset($model->$name) && $value->format('A') === 'PM' ? 'active' : '' }}"
                        >PM</a>
                    </p>
                </div>
            </div>
        </pickable>


        @if ($errors->has($name))
            <div class="help-block">{{ $errors->first($name)}}</div>
        @endif
    </div>
</div>
