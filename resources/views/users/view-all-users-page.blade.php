@extends('my_main')


@section('page-title')
    View All Users
@endsection


@section('content')
	<header class="w3-container w3-{{ $color1 }}">
		<h4 style="float:left;">Add User</h4>
		<h4 style="float:right;">
			<a class="w3-btn" href="/users/add-user-page"><i class="material-icons w3-large">person</i> Add User</a>
			<a class="w3-btn" href="/users/login-user-page"><i class="material-icons w3-large">lock</i> Login</a>
		</h4>
	</header>
	<div class="w3-container">	
		<table class="w3-table w3-bordered w3-striped">
			<thead>
				<tr>
					<th>User ID</th>
					<th>Email</th>
					<th>Name</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($users as $user)
					<tr>
						<td>{{ $user->user_id }}</td>
						<td>{{ $user->email }}</td>
						<td>{{ $user->name }}</td>
						<td><a class="w3-btn w3-{{ $color1 }}" href="users/view-user-page/{{ urlencode($user->name) }}/{{ Crypt::encrypt($user->user_id) }}" />View</a></td>
						<td><a class="w3-btn w3-{{ $color1 }}" href="users/edit-user-page/{{ Crypt::encrypt($user->user_id) }}" />Edit</a></td>
						<td><a class="w3-btn w3-red" href="users/delete-user-page/{{ Crypt::encrypt($user->user_id) }}" />Delete</a></td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
@endsection