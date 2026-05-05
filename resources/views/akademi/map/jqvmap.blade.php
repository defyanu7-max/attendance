@extends('layouts.default')
@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-lg-6">
			<div class="card">
				<div class="card-header">
					<h4 class="card-title">World Map</h4>
				</div>
				<div class="card-body">
					<div id="world-map"></div>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="card">
				<div class="card-header">
					<h4 class="card-title">USA</h4>
				</div>
				<div class="card-body">
					<div id="usa" style="height: 450px;"></div>
				</div>
			</div>
		</div>
	</div>
	 
</div>
@endsection