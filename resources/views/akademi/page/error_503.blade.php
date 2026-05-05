@extends('layouts.fullwidth')
@section('content')
<div class="col-lg-6 col-sm-12">
	<div class="form-input-content  error-page">
		<h1 class="error-text text-primary">503</h1>
		<h4>Service Unavailable</h4>
		<p>Sorry, we are under maintenance!</p>
		<a class="btn btn-primary" href="{{ url('index')}}">Back to Home</a>
	</div>
</div>
<div class="col-lg-6 col-sm-12">
	<img  class="w-100" src="images/svg/student.svg" alt="">
</div>
@endsection