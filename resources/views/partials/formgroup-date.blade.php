<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    @if ($label)
        {!! Form::label($name, $label, ['class' => 'col-sm-2 control-label']) !!}
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'col-sm-offset-2' }}">
        {!! Form::text($name, TimeHelper::dateField($model->$name), ['class' => 'form-control', 'v-model' => 'pickResult']) !!}

        @if (isset($autofill))
            <autofill-hint target="INPUT[name={{ $name }}]" v-bind:value="suggested{{ ucfirst($name) }}" v-bind:previous="previous{{ ucfirst($name) }}"></autofill-hint>
        @endif

        <pickable inline-template initial-value="{{ isset($model->$name)? $model->$name->format(TimeHelper::$dateFormat) : '' }}" target="INPUT[name={{ $name }}]">
            <div>
                <p>
                    <a href="#" @click.prevent="toggle($event)" v-bind:class="{hidden: isOpen}">
                        shortcuts
                    </a>
                </p>

                <div class="shortcuts" v-bind:class="{toggled: isOpen}">
                    <div class="well">
                        <p>
                            <span class="label">Relative:</span>
                            <a @click.prevent="pick('yyyy-mm-dd', '{{ $ranges['today']->format(TimeHelper::$dateFormat) }}')" href="#">today</a>
                            <a @click.prevent="pick('yyyy-mm-dd', '{{ $ranges['yesterday']->format(TimeHelper::$dateFormat) }}')" href="#">yesterday</a>
                            <a @click.prevent="pick('yyyy-mm-dd', '{{ $ranges['2-days-ago']->format(TimeHelper::$dateFormat) }}')" href="#">2 days ago</a>
                            <a @click.prevent="pick('yyyy-mm-dd', '{{ $ranges['3-days-ago']->format(TimeHelper::$dateFormat) }}')" href="#">3 days ago</a>
                        </p>
                        <p>
                            <span class="label">Month:</span>
                            @foreach ($ranges['month'] as $value)
                                <a
                                    @click.prevent="pick('mm', '{{ $value->format('m') }}')"
                                    href="#"
                                >{{ $value->format('M') }}</a>
                            @endforeach
                        </p>
                        <p>
                            <span class="label">Day:</span>
                            @foreach ($ranges['day'] as $value)
                                <a
                                    @click.prevent="pick('dd', '{{ $value->format('d') }}')"
                                    href="#"
                                >{{ $value->format('j') }}</a>
                            @endforeach
                        </p>
                        <p>
                            <span class="label">Year:</span>
                            @foreach ($ranges['year'] as $value)
                                <a
                                    @click.prevent="pick('yyyy', '{{ $value->format('Y') }}')"
                                    href="#"
                                >{{ $value->format('Y') }}</a>
                            @endforeach
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
