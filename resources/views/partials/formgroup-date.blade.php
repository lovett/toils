<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    @if ($label)
        {!! Form::label($name, $label, ['class' => 'col-sm-2 control-label']) !!}
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'col-sm-offset-2' }}">
        <pickable inline-template format="yyyy-mm-dd" initial="{{ date('Y-m-d') }}">
            {!! Form::text($name . $suffix, isset($model->$name)? $model->$name->format('Y-m-d') : '', ['class' => 'form-control', 'v-model' => 'pickResult']) !!}

            <p>
                <a href="#" @click.prevent="toggle($event)">
                    <span v-bind:class="{hidden: !isToggled}">hide</span> shortcuts
                </a>
            </p>

            <div class="shortcuts" v-bind:class="{toggled: isToggled}">
		<p>
		    <span class="label">Relative:</span>
                    <a @click.prevent="pick('yyyy-mm-dd', '{{ $ranges['today']->format($dateFormat) }}', 'active', $event)" href="#" >today</a>
		    <a @click.prevent="pick('yyyy-mm-dd', '{{ $ranges['yesterday']->format($dateFormat) }}', 'active', $event)" href="#" >yesterday</a>
		    <a @click.prevent="pick('yyyy-mm-dd', '{{ $ranges['2-days-ago']->format($dateFormat) }}', 'active', $event)" href="#" >2 days ago</a>
		    <a @click.prevent="pick('yyyy-mm-dd', '{{ $ranges['3-days-ago']->format($dateFormat) }}', 'active', $event)" href="#" >3 days ago</a>
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
        </pickable>

        @if ($errors->has($name))
            <div class="help-block">{{ $errors->first($name)}}</div>
        @endif
    </div>
</div>
