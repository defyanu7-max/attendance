@extends('layouts.default')
@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-header">
					<h4 class="card-title">Light Gallery</h4>
				</div>
				<div class="card-body pb-1">
					<div id="lightgallery" class="row">
						<a href="{{ asset('images/big/img1.jpg') }}" data-exthumbimage="{{ asset('images/big/img1.jpg') }}" data-src="images/big/img1.jpg" class="col-lg-3 col-6 mb-4">
							<img src="{{ asset('images/big/img1.jpg') }}" alt="" style="width:100%;">
						</a>
						<a href="images/big/img2.jpg" data-exthumbimage="images/big/img2.jpg" data-src="images/big/img2.jpg" class="col-lg-3 col-6 mb-4">
							<img src="{{ asset('images/big/img2.jpg') }}" alt="" style="width:100%;">                                                           
						</a>                                                                                                                     
						<a href="{{ asset('images/big/img3.jpg') }}" data-exthumbimage="{{ asset('images/big/img3.jpg') }}" data-src="{{ asset('images/big/img3.jpg') }}" class="col-lg-3 col-6 mb-4">
							<img src="{{ asset('images/big/img3.jpg') }}" alt="" style="width:100%;">                                                           
						</a>                                                                                                                     
						<a href="{{ asset('images/big/img4.jpg') }}" data-exthumbimage="{{ asset('images/big/img4.jpg') }}" data-src="images/big/img4.jpg" class="col-lg-3 col-6 mb-4">
							<img src="{{ asset('images/big/img4.jpg') }}" alt="" style="width:100%;">                                                           
						</a>                                                                                                                     
						<a href="{{ asset('images/big/img5.jpg') }}" data-exthumbimage="{{ asset('images/big/img5.jpg') }}" data-src="{{ asset('images/big/img5.jpg') }}" class="col-lg-3 col-6 mb-4">
							<img src="{{ asset('images/big/img5.jpg') }}" alt="" style="width:100%;">                                                           
						</a>                                                                                                                     
						<a href="{{ asset('images/big/img6.jpg') }}" data-exthumbimage="{{ asset('images/big/img6.jpg') }}" data-src="{{ asset('images/big/img6.jpg') }}" class="col-lg-3 col-6 mb-4">
							<img src="{{ asset('images/big/img6.jpg') }}" alt="" style="width:100%;">                                                           
						</a>                                                                                                                     
						<a href="{{ asset('images/big/img7.jpg') }}" data-exthumbimage="{{ asset('images/big/img7.jpg') }}" data-src="{{ asset('images/big/img7.jpg') }}" class="col-lg-3 col-6 mb-4">
							<img src="{{ asset('images/big/img7.jpg') }}" alt="" style="width:100%;">                                                           
						</a>                                                                                                                     
						<a href="{{ asset('images/big/img8.jpg') }}" data-exthumbimage="{{ asset('images/big/img8.jpg') }}" data-src="{{ asset('images/big/img8.jpg') }}" class="col-lg-3 col-6 mb-4">
							<img src="{{ asset('images/big/img8.jpg') }}" alt="" style="width:100%;">
						</a>
					</div>
				</div>
			</div>
			<!-- /# card -->
		</div>
	<div class="col-xl-4">
		<div class="card ">
			<div class="card-body p-3">
					<div id="lightgallery-1" class="row g-2">
						<a href="{{ asset('images/big/pic1.jpg') }}" data-exthumbimage="{{ asset('images/big/pic1.jpg') }}" data-src="{{ asset('images/big/pic1.jpg') }}" class="col-lg-4  col-4">
							<img src="{{ asset('images/big/pic1.jpg') }}"  alt="" style="width:100%;">
						</a>
						<a href="{{ asset('images/big/pic2.jpg') }}" data-exthumbimage="{{ asset('images/big/pic2.jpg') }}" data-src="{{ asset('images/big/pic2.jpg') }}" class="col-lg-4  col-4">
							<img src="{{ asset('images/big/pic2.jpg') }}"  alt="" style="width:100%;">
						</a>
						<a href="{{ asset('images/big/pic3.jpg') }}" data-exthumbimage="{{ asset('images/big/pic3.jpg') }}" data-src="{{ asset('images/big/pic3.jpg') }}" class="col-lg-4  col-4 ">
							<img src="{{ asset('images/big/pic3.jpg') }}"  alt="" style="width:100%;">                                                             
						</a>                                                                                                                        
						<a href="{{ asset('images/big/pic4.jpg') }}" data-exthumbimage="{{ asset('images/big/pic4.jpg') }}" data-src="{{ asset('images/big/pic4.jpg') }}" class="col-lg-4  col-4 ">
							<img src="{{ asset('images/big/pic4.jpg') }}"  alt="" style="width:100%;">                                                             
						</a>                                                                                                                        
						<a href="{{ asset('images/big/pic5.jpg') }}" data-exthumbimage="{{ asset('images/big/pic5.jpg') }}" data-src="{{ asset('images/big/pic5.jpg') }}" class="col-lg-4  col-4 ">
							<img src="{{ asset('images/big/pic5.jpg') }}"  alt="" style="width:100%;">                                                             
						</a>                                                                                                                        
						<a href="{{ asset('images/big/pic6.jpg') }}" data-exthumbimage="{{ asset('images/big/pic6.jpg') }}" data-src="{{ asset('images/big/pic6.jpg') }}" class="col-lg-4  col-4 ">
							<img src="{{ asset('images/big/pic6.jpg') }}"  alt="" style="width:100%;">                                                             
						</a>                                                                                                                        
						<a href="{{ asset('images/big/pic7.jpg') }}" data-exthumbimage="{{ asset('images/big/pic7.jpg') }}" data-src="{{ asset('images/big/pic7.jpg') }}" class="col-lg-4  col-4 ">
							<img src="{{ asset('images/big/pic7.jpg') }}"  alt="" style="width:100%;">                                                             
						</a>                                                                                                                        
						<a href="{{ asset('images/big/pic8.jpg') }}" data-exthumbimage="{{ asset('images/big/pic8.jpg') }}" data-src="{{ asset('images/big/pic8.jpg') }}" class="col-lg-4  col-4 ">
							<img src="{{ asset('images/big/pic8.jpg') }}"  alt="" style="width:100%;">                                                             
						</a>                                                                                                                        
						<a href="{{ asset('images/big/pic1.jpg') }}" data-exthumbimage="{{ asset('images/big/pic1.jpg') }}" data-src="{{ asset('images/big/pic1.jpg') }}" class="col-lg-4  col-4 ">
							<img src="{{ asset('images/big/pic1.jpg') }}"  alt="" style="width:100%;">                                                             
						</a>                                                                                                                          
					</div>
				</div>
		</div>
	</div>
	<div class="col-xl-8">
		<div class="card h-auto">
			<div class="card-body p-3">
					<div id="lightgallery-2" class="row g-2">
						<a href="{{ asset('images/big/pic9.jpg') }}" data-exthumbimage="{{ asset('images/big/pic9.jpg') }}" data-src="{{ asset('images/big/pic9.jpg') }}" class="col-lg-12  col-12">
							<img src="{{ asset('images/big/pic9.jpg') }}"  alt="" style="width:100%;">
						</a>
						<a href="{{ asset('images/big/pic2.jpg') }}" data-exthumbimage="{{ asset('images/big/pic2.jpg') }}" data-src="{{ asset('images/big/pic2.jpg') }}" class="col-lg-2  col-2">
							<img src="{{ asset('images/big/pic2.jpg') }}"  alt="" style="width:100%;">
						</a>
						<a href="{{ asset('images/big/pic3.jpg') }}" data-exthumbimage="{{ asset('images/big/pic3.jpg') }}" data-src="{{ asset('images/big/pic3.jpg') }}" class="col-lg-2  col-2 ">
							<img src="{{ asset('images/big/pic3.jpg') }}"  alt="" style="width:100%;">                                                               
						</a>                                                                                                                          
						<a href="{{ asset('images/big/pic4.jpg') }}" data-exthumbimage="{{ asset('images/big/pic4.jpg') }}" data-src="{{ asset('images/big/pic4.jpg') }}" class="col-lg-2  col-2 ">
							<img src="{{ asset('images/big/pic4.jpg') }}"  alt="" style="width:100%;">                                                               
						</a>                                                                                                                          
						<a href="{{ asset('images/big/pic5.jpg') }}" data-exthumbimage="{{ asset('images/big/pic5.jpg') }}" data-src="{{ asset('images/big/pic5.jpg') }}" class="col-lg-2  col-2 ">
							<img src="{{ asset('images/big/pic5.jpg') }}"  alt="" style="width:100%;">                                                               
						</a>                                                                                                                          
						<a href="{{ asset('images/big/pic6.jpg') }}" data-exthumbimage="{{ asset('images/big/pic6.jpg') }}" data-src="{{ asset('images/big/pic6.jpg') }}" class="col-lg-2  col-2 ">
							<img src="{{ asset('images/big/pic6.jpg') }}"  alt="" style="width:100%;">                                                               
						</a>                                                                                                                          
						<a href="{{ asset('images/big/pic7.jpg') }}" data-exthumbimage="{{ asset('images/big/pic7.jpg') }}" data-src="{{ asset('images/big/pic7.jpg') }}" class="col-lg-2  col-2">
							<img src="{{ asset('images/big/pic7.jpg') }}"  alt="" style="width:100%;">                                                               
						</a>                                                                                                                          
														 
						
					</div>
				</div>
		</div>
	</div>
	</div>
	 
</div>
@endsection