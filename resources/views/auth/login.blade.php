<form method="post" action="/auth/login">
    {!! csrf_field() !!}

    <div>
	Email
	<input type="email" name="email" value="{{ old('email') }}" />
    </div>

    <div>
	Password
	<input type="password" name="password"/>
    </div>

    <div>
	<input type="checkbox" name="remember" /> Remember me
    </div>

    <div>
	<button type="submit">Login</button>
    </div>

</form>
