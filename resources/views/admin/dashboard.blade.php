<h1>Dashboard Admin</h1>

<p>Login sebagai: {{ auth()->user()->name }}</p>

<form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit">Logout</button>
</form>