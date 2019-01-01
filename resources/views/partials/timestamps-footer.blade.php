<footer class="container">
    <div class="text-center">
        <span class="field">Updated {{ TimeHelper::longDate($record->updated_at) }} at {{ TimeHelper::time($record->updated_at) }}.</span>
        <span class="field">Created {{ TimeHelper::date($record->created_at) }} at {{ TimeHelper::time($record->created_at) }}.</span>
    </div>
</footer>
