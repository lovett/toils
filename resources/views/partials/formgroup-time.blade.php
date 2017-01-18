<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    @if ($label)
        {!! Form::label($name, $label, ['class' => 'col-sm-2 control-label']) !!}
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'col-sm-offset-2' }}">
        {!! Form::text($fieldName, TimeHelper::timeField($model->$name), ['class' => 'form-control']) !!}

        <pickable inline-template format="hh:mm AA" initial-value="{{ date('h:i A') }}" target="INPUT[name={{ $fieldName }}]">
            <div>
                <p>
                    <a href="#" @click.prevent="toggle($event)" v-bind:class="{hidden: isOpen}">
                        shortcuts
                    </a>
                </p>

                <div class="shortcuts" v-bind:class="{toggled: isOpen}">
                    <div class="well">
                        <p>
                            <span class="label">Hour:</span>
                            @foreach ($ranges['hour'] as $value)
                                <a @click.prevent="pick('hh', '{{ $value->format('g') }}')"
                                   href="#"
                                >{{ $value->format('g') }}</a>
                            @endforeach
                        </p>
                        <p>
                            <span class="label">Minute:</span>
                            @foreach ($ranges['minute'] as $value)
                                <a @click.prevent="pick('mm', '{{ $value->format('i') }}')"
                                   href="#"
                                >{{ $value->format('i') }}</a>
                            @endforeach
                        </p>
                        <p>
                            <a @click.prevent="pick('AA', 'AM')"
                               href="#"
                            >AM</a>
                            <a @click.prevent="pick('AA', 'PM')"
                               href="#"
                            >PM</a>
                        </p>

                        <p class="text-center">
                            <a class="small" href="#" @click.prevent="toggle($event)">
                                ▲
                            </a>
                        </p>

                    </div>
                </div>
            </div>
        </pickable>

        @if ($errors->has($name))
            <div class="help-block">{{ $errors->first($name)}}</div>
        @endif
    </div>
</div>
