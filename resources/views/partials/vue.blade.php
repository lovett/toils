@if (env('APP_DEBUG'))
<script src="{{ asset('js/vue.js') }}"></script>
@else
<script src="{{ asset('js/vue.min.js') }}"></script>
@endif
