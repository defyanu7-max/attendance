@extends('layouts.fullwidth')
@section('content')
<div class="col-lg-6 col-sm-12">
	<div class="form-input-content  error-page">
		<h1 class="error-text text-primary">404</h1>
		<h4> The page you were looking for is not found!</h4>
		<p>You may have mistyped the address or the page may have moved.</p>
		<a class="btn btn-primary" href="{{ url('index')}}">Back to Home</a>
	</div>
</div>
<div class="col-lg-6 col-sm-12">
	<img  class="w-100" src="images/svg/student.svg" alt="">
</div>
@endsection