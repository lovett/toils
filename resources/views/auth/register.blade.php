<form method="post" action="/auth/register">
    {!! csrf_field() !!}

    @if (count($errors) > 0)
    <div>
	<ul>
	    @foreach ($errors->all() as $error)
	    <li>{{ $error }}</li>
	    @endforeach
	</ul>
    </div>
    @endif

    <div>
	Name
	<input type="text" name="name" value="{{ old('name') }}" />
    </div>

    <div>
	Email
	<input type="email" name="email" value="{{ old('email') }}" />
    </div>

    <div>
	Password
	<input type="password" name="password" />
    </div>

    <div>
	Confirm password
	<input type="password" name="password_confirmation" />
    </div>

    <div>
	<button type="submit">Register</button>
    </div>
</form>
