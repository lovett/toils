<script src="{{ asset('js/select2.min.js') }}"></script>
<script type="text/javascript">
    $('select').select2(
        {'theme': 'bootstrap'}
    ).on('change', function (e) {
        vueBus.$emit('menu-changed', e)
    });
</script>
