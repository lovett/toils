<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    @if ($label)
        {!! Form::label($name, $label, ['class' => 'col-sm-2 control-label']) !!}
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'col-sm-offset-2' }}">
        {!! Form::text($name, TimeHelper::dateField($model->$name), ['class' => 'form-control']) !!}

        @if (isset($autofill))
            <autofill-hint target="INPUT[name={{ $name }}]" v-bind:value="{{ $name }}"></autofill-hint>
        @endif

        <pickable inline-template initial-value="{{ isset($model->$name)? $model->$name->format(TimeHelper::$dateFormat) : '' }}" target="INPUT[name={{ $name }}]">
            <div>
                <p>
                    <a href="#" @click.prevent="toggle($event)">
                        <span v-bind:class="{hidden: !isOpen}">hide</span> shortcuts
                    </a>
                </p>

                <div class="shortcuts" v-bind:class="{toggled: isOpen}">
                    <p>
                        <span class="label">Relative:</span>
                        <a @click.prevent="pick('yyyy-mm-dd', '{{ $ranges['today']->format(TimeHelper::$dateFormat) }}', 'active', $event)" href="#" >today</a>
                        <a @click.prevent="pick('yyyy-mm-dd', '{{ $ranges['yesterday']->format(TimeHelper::$dateFormat) }}', 'active', $event)" href="#" >yesterday</a>
                        <a @click.prevent="pick('yyyy-mm-dd', '{{ $ranges['2-days-ago']->format(TimeHelper::$dateFormat) }}', 'active', $event)" href="#" >2 days ago</a>
                        <a @click.prevent="pick('yyyy-mm-dd', '{{ $ranges['3-days-ago']->format(TimeHelper::$dateFormat) }}', 'active', $event)" href="#" >3 days ago</a>
                    </p>
                    <p>
                        <span class="label">Month:</span>
                        @foreach ($ranges['month'] as $value)
                            <a
                                @click.prevent="pick('mm', '{{ $value->format('m') }}', 'active', $event)"
                                href="#"
                                class="{{ $value->format('M') === $model->$name->format('M') ? 'active' : '' }}"
                            >{{ $value->format('M') }}</a>
                        @endforeach
                    </p>
                    <p>
                        <span class="label">Day:</span>
                        @foreach ($ranges['day'] as $value)
                            <a
                                @click.prevent="pick('dd', '{{ $value->format('d') }}', 'active', $event)"
                                href="#"
                                class="{{ $value->format('j') === $model->$name->format('j') ? 'active' : '' }}"
                            >{{ $value->format('j') }}</a>
                        @endforeach
                    </p>
                    <p>
                        <span class="label">Year:</span>
                        @foreach ($ranges['year'] as $value)
                            <a
                                @click.prevent="pick('yyyy', '{{ $value->format('Y') }}', 'active', $event)"
                                href="#"
                                class="{{ $value->format('Y') === $model->$name->format('Y') ? 'active' : '' }}"
                            >{{ $value->format('Y') }}</a>
                        @endforeach
                    </p>
                </div>
            </div>
        </pickable>

        @if ($errors->has($name))
            <div class="help-block">{{ $errors->first($name)}}</div>
        @endif
    </div>
</div>
