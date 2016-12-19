<footer class="timestamps row">
    <div class="col-sm-12 text-center">
        <div class="well">
            <span class="field">Updated {{ $record->updated_at->format('\a\t g:i A \o\n Y-m-d') }}.</span>
            <span class="field">Created on {{ $record->created_at->format('Y-m-d') }}.</span>
        </div>
    </div>
</footer>
