@if ($value === true)
    <svg class="icon active"><use xlink:href="#icon-checkmark"></use></svg>
    active
@else
    <svg class="icon inactive"><use xlink:href="#icon-blocked"></use></svg>
    inactive
@endif
