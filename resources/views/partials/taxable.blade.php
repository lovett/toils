@if ($value === true)
    <svg class="icon active"><use xlink:href="#icon-library"></use></svg>
taxes deducted
@else
    <svg class="icon inactive"><use xlink:href="#icon-library"></use></svg>
taxes not deducted
@endif
