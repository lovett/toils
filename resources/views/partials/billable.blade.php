@if ($value === true)
    <svg class="icon active"><use xlink:href="#icon-coin-dollar"></use></svg>
billable
@else
    <svg class="icon inactive"><use xlink:href="#icon-coin-dollar"></use></svg>
unbillable
@endif
