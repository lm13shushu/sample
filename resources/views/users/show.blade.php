@extends('layouts.default')
@section('title',$user->name)
@section('content')
<div class="row">
	<div class="col-md-offset-3 col-md-6">
		<div class="col-md-12">
			  <div class="col-md-offset-2 col-md-8">
				    <section class="user_info">
				      @include('shared._user_info',['user'=>$user])
				    </section>
			  </div>
		</div>
	</div>
</div>
	<!-- {{$user->name}}-{{$user->email}} -->
@stop