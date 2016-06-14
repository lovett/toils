<tr>
    <td>
	<a href="{{ route('time.show', ['record' => $record]) }}">
	    {{ $record->start->format('Y-m-d') }}
	</a>
    </td>
    <td>
	{{ $record->start->format('l') }}
    </td>
    <td>
	{{ $record->start->format('g:i A') }}
    </td>
    <td>
	{{ $record->end->format('g:i A') }}
    </td>
    <td>
	{{ $record->project->name }}
    </td>
</tr>
