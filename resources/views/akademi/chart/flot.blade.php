@extends('layouts.default')
@section('content')
<div class="container-fluid">
	<!-- row -->
	<div class="row">
		<div class="col-12">
			<div class="row">
				<div class="col-xl-12">
					<div class="page-titles">
						<div class="d-flex align-items-center">
							<h2 class="heading">Chat Flot</h2>
							<!-- <p class="text-warning ms-2">Welcome Back Yatin Sharma !</p> -->
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-sm-6">
					<div class="card">
						<div class="card-header">
							<h4 class="card-title">Bar Chart</h4>
						</div>
						<div class="card-body">
							<div id="flotBar1" class="flot-chart"></div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-sm-6">
					<div class="card">
						<div class="card-header">
							<h4 class="card-title">Bar Chart</h4>
						</div>
						<div class="card-body">
							<div id="flotBar2" class="flot-chart"></div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-sm-6">
					<div class="card">
						<div class="card-header">
							<h4 class="card-title">Line Chart</h4>
						</div>
						<div class="card-body">
							<div id="flotLine1" class="flot-chart"></div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-sm-6">
					<div class="card">
						<div class="card-header">
							<h4 class="card-title">Line Chart</h4>
						</div>
						<div class="card-body">
							<div id="flotLine2" class="flot-chart"></div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-sm-6">
					<div class="card">
						<div class="card-header">
							<h4 class="card-title">Line Chart</h4>
						</div>
						<div class="card-body">
							<div id="flotLine3" class="flot-chart"></div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-sm-6">
					<div class="card">
						<div class="card-header">
							<h4 class="card-title">Area Chart</h4>
						</div>
						<div class="card-body">
							<div id="flotArea1" class="flot-chart"></div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-sm-6">
					<div class="card">
						<div class="card-header">
							<h4 class="card-title">Area Chart</h4>
						</div>
						<div class="card-body">
							<div id="flotArea2" class="flot-chart"></div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-sm-6">
					<div class="card">
						<div class="card-header">
							<h4 class="card-title">Realtime Chart</h4>
						</div>
						<div class="card-body">
							<div id="flotRealtime1" class="flot-chart"></div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-sm-6">
					<div class="card">
						<div class="card-header">
							<h4 class="card-title">Realtime Chart</h4>
						</div>
						<div class="card-body">
							<div id="flotRealtime2" class="flot-chart"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection