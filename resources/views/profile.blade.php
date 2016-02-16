@extends('layouts.front')

@section('content')

	<article class="article">
	    <h1 class="article__header">Profile</h1>
		
		<div class="content">

			<div class="profile">

				<img class="profile__avatar" src="{{ $user->gravatar }}" />

				<div class="identity">

					<div class="identity__heading">
						<p class="identity__name profile__line-item">{{ $user->first_name }} {{ $user->last_name }}</p>
						<p class="identity__location profile__line-item">{{ $user->location }}</p>
					</div>

					<div class="identity_position">
						<p class="identity__title profile__line-item">
							<span class="profile__line-item__label">Title:</span> {{ $user->title }}
						</p>
						<p class="identity__email profile__line-item">
							<span class="profile__line-item__label">Email:</span> <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
						</p>
						<p class="identity__department profile__line-item">
							<span class="profile__line-item__label">Department:</span> {{ $user->department }}
						</p>
					</div>
					
				</div>
			</div>

		</div>

	</article>


@endsection