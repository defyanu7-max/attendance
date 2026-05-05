@php
    $controller = DzHelper::controller();
    $page = $action = DzHelper::action();
    $action = $controller.'_'.$action;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="author" content="DexignLab" >
	<meta name="robots" content="" >
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="keywords" content="school, school admin, education, academy, admin dashboard, college, college management, education management, institute, school management, school management system, student management, teacher management, university, university management" >
	<meta name="description" content="@yield('page_description', $page_description ?? '') >
	<meta property="og:title" content="Akademi : School and Education Management Admin Dashboard Template" >
	<meta property="og:description" content="@yield('page_description', $page_description ?? '')">
	<meta property="og:image" content="https://akademi.dexignlab.com/Laravel/social-image.png" >
	<meta name="format-detection" content="telephone=no">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Page Title Here -->
	<title>{{ config('dz.name') }} | @yield('title', $page_title ?? '')</title> 

<!-- FAVICONS ICON -->
    <link rel="shortcut icon" type="image/png" href="{{asset('images/favicon.png') }}">
    @if(!empty(config('dz.public.pagelevel.css.'.$action))) 
        @foreach(config('dz.public.pagelevel.css.'.$action) as $style)
            <link href="{{ asset($style) }}" rel="stylesheet" type="text/css"/>
        @endforeach
    @endif

    {{-- Global Theme Styles (used by all pages) --}}
    @if(!empty(config('dz.public.global.css'))) 
        @foreach(config('dz.public.global.css') as $style)
            <link href="{{ asset($style) }}" rel="stylesheet" type="text/css"/>
        @endforeach
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
	
</head>
<body>

    <!--*******************
        Preloader start
    ********************-->
	<div id="preloader">
	  <div class="loader">
		<div class="dots">
			  <div class="dot mainDot"></div>
			  <div class="dot"></div>
			  <div class="dot"></div>
			  <div class="dot"></div>
			  <div class="dot"></div>
		</div>
			
		</div>
	</div>
    <!--*******************
        Preloader end
    ********************-->
    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper" class="{{ in_array($page,array('dashboard','dashboard_2')) ? 'wallet-open active' : '' }}">
	
        <!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header">
            <a href="{{url('index')}}" class="brand-logo">
				<svg class="logo-abbr" width="40" height="40" viewBox="0 0 48 54" fill="none" xmlns="http://www.w3.org/2000/svg">
					<rect y="3" width="48" height="48" rx="16" fill="#FB7D5B"/>
					<path d="M28.964 35.536H19.532L18.02 40H11.576L20.72 14.728H27.848L36.992 40H30.476L28.964 35.536ZM27.38 30.784L24.248 21.532L21.152 30.784H27.38Z" fill="white"/>
				</svg>
				<div class="brand-title">
					<svg width="140" height="30" viewBox="0 0 167 30" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M17.964 24.536H8.532L7.02 29H0.576L9.72 3.728H16.848L25.992 29H19.476L17.964 24.536ZM16.38 19.784L13.248 10.532L10.152 19.784H16.38ZM41.051 29L34.931 20.576V29H28.775V2.36H34.931V17.084L41.015 8.912H48.611L40.259 18.992L48.683 29H41.051ZM49.8049 18.92C49.8049 16.856 50.1889 15.044 50.9569 13.484C51.7489 11.924 52.8169 10.724 54.1609 9.884C55.5049 9.044 57.0049 8.624 58.6609 8.624C60.0769 8.624 61.3129 8.912 62.3689 9.488C63.4489 10.064 64.2769 10.82 64.8529 11.756V8.912H71.0089V29H64.8529V26.156C64.2529 27.092 63.4129 27.848 62.3329 28.424C61.2769 29 60.0409 29.288 58.6249 29.288C56.9929 29.288 55.5049 28.868 54.1609 28.028C52.8169 27.164 51.7489 25.952 50.9569 24.392C50.1889 22.808 49.8049 20.984 49.8049 18.92ZM64.8529 18.956C64.8529 17.42 64.4209 16.208 63.5569 15.32C62.7169 14.432 61.6849 13.988 60.4609 13.988C59.2369 13.988 58.1929 14.432 57.3289 15.32C56.4889 16.184 56.0689 17.384 56.0689 18.92C56.0689 20.456 56.4889 21.68 57.3289 22.592C58.1929 23.48 59.2369 23.924 60.4609 23.924C61.6849 23.924 62.7169 23.48 63.5569 22.592C64.4209 21.704 64.8529 20.492 64.8529 18.956ZM74.2385 18.92C74.2385 16.856 74.6225 15.044 75.3905 13.484C76.1825 11.924 77.2505 10.724 78.5945 9.884C79.9385 9.044 81.4385 8.624 83.0945 8.624C84.4145 8.624 85.6145 8.9 86.6945 9.452C87.7985 10.004 88.6625 10.748 89.2865 11.684V2.36H95.4425V29H89.2865V26.12C88.7105 27.08 87.8825 27.848 86.8025 28.424C85.7465 29 84.5105 29.288 83.0945 29.288C81.4385 29.288 79.9385 28.868 78.5945 28.028C77.2505 27.164 76.1825 25.952 75.3905 24.392C74.6225 22.808 74.2385 20.984 74.2385 18.92ZM89.2865 18.956C89.2865 17.42 88.8545 16.208 87.9905 15.32C87.1505 14.432 86.1185 13.988 84.8945 13.988C83.6705 13.988 82.6265 14.432 81.7625 15.32C80.9225 16.184 80.5025 17.384 80.5025 18.92C80.5025 20.456 80.9225 21.68 81.7625 22.592C82.6265 23.48 83.6705 23.924 84.8945 23.924C86.1185 23.924 87.1505 23.48 87.9905 22.592C88.8545 21.704 89.2865 20.492 89.2865 18.956ZM118.832 18.632C118.832 19.208 118.796 19.808 118.724 20.432H104.792C104.888 21.68 105.284 22.64 105.98 23.312C106.7 23.96 107.576 24.284 108.608 24.284C110.144 24.284 111.212 23.636 111.812 22.34H118.364C118.028 23.66 117.416 24.848 116.528 25.904C115.664 26.96 114.572 27.788 113.252 28.388C111.932 28.988 110.456 29.288 108.824 29.288C106.856 29.288 105.104 28.868 103.568 28.028C102.032 27.188 100.832 25.988 99.9681 24.428C99.1041 22.868 98.6721 21.044 98.6721 18.956C98.6721 16.868 99.0921 15.044 99.9321 13.484C100.796 11.924 101.996 10.724 103.532 9.884C105.068 9.044 106.832 8.624 108.824 8.624C110.768 8.624 112.496 9.032 114.008 9.848C115.52 10.664 116.696 11.828 117.536 13.34C118.4 14.852 118.832 16.616 118.832 18.632ZM112.532 17.012C112.532 15.956 112.172 15.116 111.452 14.492C110.732 13.868 109.832 13.556 108.752 13.556C107.72 13.556 106.844 13.856 106.124 14.456C105.428 15.056 104.996 15.908 104.828 17.012H112.532ZM147.712 8.696C150.208 8.696 152.188 9.452 153.652 10.964C155.14 12.476 155.884 14.576 155.884 17.264V29H149.764V18.092C149.764 16.796 149.416 15.8 148.72 15.104C148.048 14.384 147.112 14.024 145.912 14.024C144.712 14.024 143.764 14.384 143.068 15.104C142.396 15.8 142.06 16.796 142.06 18.092V29H135.94V18.092C135.94 16.796 135.592 15.8 134.896 15.104C134.224 14.384 133.288 14.024 132.088 14.024C130.888 14.024 129.94 14.384 129.244 15.104C128.572 15.8 128.236 16.796 128.236 18.092V29H122.08V8.912H128.236V11.432C128.86 10.592 129.676 9.932 130.684 9.452C131.692 8.948 132.832 8.696 134.104 8.696C135.616 8.696 136.96 9.02 138.136 9.668C139.336 10.316 140.272 11.24 140.944 12.44C141.64 11.336 142.588 10.436 143.788 9.74C144.988 9.044 146.296 8.696 147.712 8.696ZM163.285 6.824C162.205 6.824 161.317 6.512 160.621 5.888C159.949 5.24 159.613 4.448 159.613 3.512C159.613 2.552 159.949 1.76 160.621 1.136C161.317 0.487998 162.205 0.163998 163.285 0.163998C164.341 0.163998 165.205 0.487998 165.877 1.136C166.573 1.76 166.921 2.552 166.921 3.512C166.921 4.448 166.573 5.24 165.877 5.888C165.205 6.512 164.341 6.824 163.285 6.824ZM166.345 8.912V29H160.189V8.912H166.345Z" fill="white"/>
					</svg>
				</div> 
            </a>
			

            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
					<svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect x="22" y="11" width="4" height="4" rx="2" fill="#2A353A"/>
						<rect x="11" width="4" height="4" rx="2" fill="#2A353A"/>
						<rect x="22" width="4" height="4" rx="2" fill="#2A353A"/>
						<rect x="11" y="11" width="4" height="4" rx="2" fill="#2A353A"/>
						<rect x="11" y="22" width="4" height="4" rx="2" fill="#2A353A"/>
						<rect width="4" height="4" rx="2" fill="#2A353A"/>
						<rect y="11" width="4" height="4" rx="2" fill="#2A353A"/>
						<rect x="22" y="22" width="4" height="4" rx="2" fill="#2A353A"/>
						<rect y="22" width="4" height="4" rx="2" fill="#2A353A"/>
					</svg>		
                </div>
            </div>
        </div>
       
         <!--**********************************
            Nav header end
        ***********************************-->
		
		<!--**********************************
            Chat box start
        ***********************************-->
        @include('elements.header')
			
        
        <!--**********************************
            Sidebar start
        ***********************************-->
			@include('elements.sidebar')
        <!--**********************************
            Sidebar end
        ***********************************-->
		
		<!--****
		Wallet Sidebar
		****-->
        @if (in_array($page,array('dashboard','dashboard_2')))
            <div class="wallet-bar wow fadeInRight dlab-scroll active" id="wallet-bar" data-wow-delay="0.7s">		
                <div class="row ">
                    <!--column-->
                    <div class="col-xl-12">
                        <div class="card bg-transparent mb-1">
                            <div class="card-header border-0 px-3">
                                <div>
                                    <h2 class="heading mb-0">Recent Students</h2>
                                    <span>You have <span class="font-w600">456</span> Students</span>
                                </div>
                                <div >
                                    <a href="#" class="add icon-box bg-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5.188 13.412V8.512H0.428V5.348H5.188V0.531999H8.352V5.348H13.14V8.512H8.352V13.412H5.188Z" fill="white"/>
                                        </svg>
                                    </a>									
                                </div>	
                            </div>
                            <div class="card-body height450 dlab-scroll loadmore-content recent-activity-wrapper p-3 pt-2" id="RecentActivityContent">
                                <!--student-->
                                <div class="d-flex align-items-center student">
                                    <span class="dz-media">
                                        <img src="{{ asset('images/profile/small/pic1.jpg') }}" alt="" width="50" class="avatar">
                                    </span>
                                    <div class="user-info">
                                        <h6 class="name"><a href="{{ url('app-profile')}}">Samantha William</a></h6>
                                        <span class="fs-14 font-w400 text-wrap">Class VII A</span>
                                    </div>
                                    <div class="icon-box st-box ms-auto">
                                        <a href="#">
                                            <svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19 0.000114441H3C2.20435 0.000114441 1.44129 0.316185 0.878679 0.878794C0.31607 1.4414 0 2.20446 0 3.00011V13.0001C0 13.7958 0.31607 14.5588 0.878679 15.1214C1.44129 15.684 2.20435 16.0001 3 16.0001H19C19.7956 16.0001 20.5587 15.684 21.1213 15.1214C21.6839 14.5588 22 13.7958 22 13.0001V3.00011C22 2.20446 21.6839 1.4414 21.1213 0.878794C20.5587 0.316185 19.7956 0.000114441 19 0.000114441V0.000114441ZM20 12.7501L15.1 8.35011L20 4.92011V12.7501ZM2 4.92011L6.9 8.35011L2 12.7501V4.92011ZM8.58 9.53011L10.43 10.8201C10.5974 10.9362 10.7963 10.9985 11 10.9985C11.2037 10.9985 11.4026 10.9362 11.57 10.8201L13.42 9.53011L18.42 14.0001H3.6L8.58 9.53011ZM3 2.00011H19C19.1857 2.0016 19.3673 2.05478 19.5245 2.15369C19.6817 2.2526 19.8083 2.39333 19.89 2.56011L11 8.78011L2.11 2.56011C2.19171 2.39333 2.31826 2.2526 2.47545 2.15369C2.63265 2.05478 2.81428 2.0016 3 2.00011V2.00011Z" fill="#A098AE"/>
                                            </svg>
                                        </a>		
                                    </div>													
                                </div>
                                <!--/student-->
                                <!--student-->
                                <div class="d-flex align-items-center student">
                                    <span class="dz-media">
                                        <img src="{{ asset('images/profile/small/pic2.jpg') }}" alt="" width="50" class="avatar">
                                    </span>
                                    <div class="user-info">
                                        <h6 class="name"><a href="{{ url('app-profile')}}">Tony Soap</a></h6>
                                        <span class=" text-wrap text-break">Class VII B</span>
                                    </div>
                                    <div class="icon-box st-box ms-auto">
                                        <a href="#">
                                            <svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19 0.000114441H3C2.20435 0.000114441 1.44129 0.316185 0.878679 0.878794C0.31607 1.4414 0 2.20446 0 3.00011V13.0001C0 13.7958 0.31607 14.5588 0.878679 15.1214C1.44129 15.684 2.20435 16.0001 3 16.0001H19C19.7956 16.0001 20.5587 15.684 21.1213 15.1214C21.6839 14.5588 22 13.7958 22 13.0001V3.00011C22 2.20446 21.6839 1.4414 21.1213 0.878794C20.5587 0.316185 19.7956 0.000114441 19 0.000114441V0.000114441ZM20 12.7501L15.1 8.35011L20 4.92011V12.7501ZM2 4.92011L6.9 8.35011L2 12.7501V4.92011ZM8.58 9.53011L10.43 10.8201C10.5974 10.9362 10.7963 10.9985 11 10.9985C11.2037 10.9985 11.4026 10.9362 11.57 10.8201L13.42 9.53011L18.42 14.0001H3.6L8.58 9.53011ZM3 2.00011H19C19.1857 2.0016 19.3673 2.05478 19.5245 2.15369C19.6817 2.2526 19.8083 2.39333 19.89 2.56011L11 8.78011L2.11 2.56011C2.19171 2.39333 2.31826 2.2526 2.47545 2.15369C2.63265 2.05478 2.81428 2.0016 3 2.00011V2.00011Z" fill="#A098AE"/>
                                            </svg>
                                        </a>		
                                    </div>																				
                                </div>
                                <!--/student-->
                                <!--student-->
                                <div class="d-flex align-items-center student">
                                    <span class="dz-media">
                                        <img src="{{ asset('images/profile/small/pic3.jpg') }}" alt="" class="avatar">
                                    </span>
                                    <div class="user-info">
                                        <h6 class="name"><a href="{{ url('app-profile')}}">Karen Hope</a></h6>
                                        <span>Web Developer</span>
                                    </div>
                                    <div class="icon-box st-box ms-auto">
                                        <a href="#">
                                            <svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19 0.000114441H3C2.20435 0.000114441 1.44129 0.316185 0.878679 0.878794C0.31607 1.4414 0 2.20446 0 3.00011V13.0001C0 13.7958 0.31607 14.5588 0.878679 15.1214C1.44129 15.684 2.20435 16.0001 3 16.0001H19C19.7956 16.0001 20.5587 15.684 21.1213 15.1214C21.6839 14.5588 22 13.7958 22 13.0001V3.00011C22 2.20446 21.6839 1.4414 21.1213 0.878794C20.5587 0.316185 19.7956 0.000114441 19 0.000114441V0.000114441ZM20 12.7501L15.1 8.35011L20 4.92011V12.7501ZM2 4.92011L6.9 8.35011L2 12.7501V4.92011ZM8.58 9.53011L10.43 10.8201C10.5974 10.9362 10.7963 10.9985 11 10.9985C11.2037 10.9985 11.4026 10.9362 11.57 10.8201L13.42 9.53011L18.42 14.0001H3.6L8.58 9.53011ZM3 2.00011H19C19.1857 2.0016 19.3673 2.05478 19.5245 2.15369C19.6817 2.2526 19.8083 2.39333 19.89 2.56011L11 8.78011L2.11 2.56011C2.19171 2.39333 2.31826 2.2526 2.47545 2.15369C2.63265 2.05478 2.81428 2.0016 3 2.00011V2.00011Z" fill="#A098AE"/>
                                            </svg>
                                        </a>		
                                    </div>																			
                                </div>
                                <!--/student-->
                                <!--student-->
                                <div class="d-flex align-items-center student">
                                    <span class="dz-media">
                                        <img src="{{ asset('images/profile/small/pic4.jpg') }}" alt="" class="avatar">
                                    </span>
                                    <div class="user-info">
                                        <h6 class="name"><a href="{{ url('app-profile')}}">Jordan Nico</a></h6>
                                        <span class=" text-wrap">Class VII A</span>
                                    </div>
                                    <div class="icon-box st-box ms-auto">
                                        <a href="#">
                                            <svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19 0.000114441H3C2.20435 0.000114441 1.44129 0.316185 0.878679 0.878794C0.31607 1.4414 0 2.20446 0 3.00011V13.0001C0 13.7958 0.31607 14.5588 0.878679 15.1214C1.44129 15.684 2.20435 16.0001 3 16.0001H19C19.7956 16.0001 20.5587 15.684 21.1213 15.1214C21.6839 14.5588 22 13.7958 22 13.0001V3.00011C22 2.20446 21.6839 1.4414 21.1213 0.878794C20.5587 0.316185 19.7956 0.000114441 19 0.000114441V0.000114441ZM20 12.7501L15.1 8.35011L20 4.92011V12.7501ZM2 4.92011L6.9 8.35011L2 12.7501V4.92011ZM8.58 9.53011L10.43 10.8201C10.5974 10.9362 10.7963 10.9985 11 10.9985C11.2037 10.9985 11.4026 10.9362 11.57 10.8201L13.42 9.53011L18.42 14.0001H3.6L8.58 9.53011ZM3 2.00011H19C19.1857 2.0016 19.3673 2.05478 19.5245 2.15369C19.6817 2.2526 19.8083 2.39333 19.89 2.56011L11 8.78011L2.11 2.56011C2.19171 2.39333 2.31826 2.2526 2.47545 2.15369C2.63265 2.05478 2.81428 2.0016 3 2.00011V2.00011Z" fill="#A098AE"/>
                                            </svg>
                                        </a>		
                                    </div>																				
                                </div>
                                <!--/student-->
                                <!--student-->
                                <div class="d-flex align-items-center student">
                                    <span class="dz-media">
                                        <img src="{{asset('images/profile/small/pic5.jpg')}}" alt="" class="avatar">
                                    </span>
                                    <div class="user-info">
                                        <h6 class="name"><a href="{{ url('app-profile')}}">Nadila Adja</a></h6>
                                        <span class=" text-wrap">Class VII B</span>
                                    </div>
                                    <div class="icon-box st-box ms-auto">
                                        <a href="#">
                                            <svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19 0.000114441H3C2.20435 0.000114441 1.44129 0.316185 0.878679 0.878794C0.31607 1.4414 0 2.20446 0 3.00011V13.0001C0 13.7958 0.31607 14.5588 0.878679 15.1214C1.44129 15.684 2.20435 16.0001 3 16.0001H19C19.7956 16.0001 20.5587 15.684 21.1213 15.1214C21.6839 14.5588 22 13.7958 22 13.0001V3.00011C22 2.20446 21.6839 1.4414 21.1213 0.878794C20.5587 0.316185 19.7956 0.000114441 19 0.000114441V0.000114441ZM20 12.7501L15.1 8.35011L20 4.92011V12.7501ZM2 4.92011L6.9 8.35011L2 12.7501V4.92011ZM8.58 9.53011L10.43 10.8201C10.5974 10.9362 10.7963 10.9985 11 10.9985C11.2037 10.9985 11.4026 10.9362 11.57 10.8201L13.42 9.53011L18.42 14.0001H3.6L8.58 9.53011ZM3 2.00011H19C19.1857 2.0016 19.3673 2.05478 19.5245 2.15369C19.6817 2.2526 19.8083 2.39333 19.89 2.56011L11 8.78011L2.11 2.56011C2.19171 2.39333 2.31826 2.2526 2.47545 2.15369C2.63265 2.05478 2.81428 2.0016 3 2.00011V2.00011Z" fill="#A098AE"/>
                                            </svg>
                                        </a>		
                                    </div>																				
                                </div>
                                <!--/student-->
                            </div>
                            <div class="card-footer text-center border-0 pt-0 px-3 pb-0">
                                <a href="javascript:void(0);" class="btn btn-block btn-primary light btn-rounded dlab-load-more" id="RecentActivity" rel="{{ route('ajax_recentactivity')}}">View More</a>
                            </div>
                        </div>
                    </div>
                    <!--/column-->
                    <!--column-->
                    <div class="col-xl-12">
                        <div class="card massage bg-transparent mb-0">
                            <div class="card-header border-0 px-3 py-0">
                                <div>
                                    <h2 class="heading">Messages</h2>
                                </div>
                                
                            </div>
                            <div class="card-body height450 dlab-scroll p-0 px-3" id="messageContent">
                                <!--student-->
                                <div class="d-flex student border-bottom">
                                    <div class="d-flex align-items-center">
                                        <span class="dz-media">
                                            <img src="{{ asset('images/profile/small/pic1.jpg') }}" alt="" class="avatar">
                                        </span>
                                        <div class="user-info">
                                            <h6 class="name"><a href="{{ url('app-profile')}}">Samantha William</a></h6>
                                            <span class="fs-14 font-w400 text-wrap">Lorem ipsum dolor sit</span>
                                        </div>
                                    </div>													
                                    <div class="indox">
                                        <span class="fs-14 font-w400 text-wrap">12:45 PM</span>		
                                    </div>
                                </div>
                                <!--student-->
                                <div class="d-flex student border-bottom">
                                    <div class="d-flex align-items-center">
                                        <span class="dz-media">
                                            <img src="{{ asset('images/profile/small/pic2.jpg') }}" alt="" class="avatar">
                                        </span>
                                        <div class="user-info">
                                            <h6 class="name"><a href="{{ url('app-profile')}}">Tony Soap</a></h6>
                                            <span class="fs-14 font-w400 text-wrap">Lorem ipsum dolor sit</span>
                                        </div>
                                    </div>													
                                    <div class="indox">
                                        <span class="fs-14 font-w400 text-wrap">12:45 PM</span>		
                                    </div>
                                </div>
                                <!--student-->
                                <div class="d-flex student border-bottom">
                                    <div class="d-flex align-items-center">
                                        <span class="dz-media">
                                            <img src="{{ asset('images/profile/small/pic2.jpg') }}" alt="" class="avatar">
                                        </span>
                                        <div class="user-info">
                                            <h6 class="name"><a href="{{ url('app-profile')}}">Tony Soap</a></h6>
                                            <span class="fs-14 font-w400 text-wrap">Lorem ipsum dolor sit</span>
                                        </div>
                                    </div>													
                                    <div class="indox">
                                        <span class="fs-14 font-w400 text-wrap">12:45 PM</span>		
                                    </div>
                                </div>
                                <!--tudent-->
                                <div class="d-flex student border-bottom">
                                    <div class="d-flex align-items-center">
                                        <span class="dz-media">
                                            <img src="{{ asset('images/profile/small/pic3.jpg') }}" alt="" class="avatar">
                                        </span>
                                        <div class="user-info">
                                            <h6 class="name"><a href="{{ url('app-profile')}}">Jordan Nico</a></h6>
                                            <span class="fs-14 font-w400 text-wrap">Lorem ipsum dolor sit</span>
                                        </div>
                                    </div>													
                                    <div class="indox">
                                        <span class="fs-14 font-w400 text-wrap">12:45 PM</span>		
                                    </div>
                                </div>
                                <!--student-->
                                <div class="d-flex student border-bottom">
                                    <div class="d-flex align-items-center">
                                        <span class="dz-media">
                                            <img src="{{ asset('images/profile/small/pic4.jpg') }}" alt="" class="avatar">
                                        </span>
                                        <div class="user-info">
                                            <h6 class="name"><a href="{{ url('app-profile')}}">Nadila Adja</a></h6>
                                            <span class="fs-14 font-w400 text-wrap">Lorem ipsum dolor sit</span>
                                        </div>
                                    </div>													
                                    <div class="indox">
                                        <span class="fs-14 font-w400 text-wrap">12:45 PM</span>		
                                    </div>
                                </div>
                                <div class="d-flex student border-bottom">
                                    <div class="d-flex align-items-center">
                                        <span class="dz-media">
                                            <img src="{{ asset('images/profile/small/pic4.jpg') }}" alt="" class="avatar">
                                        </span>
                                        <div class="user-info">
                                            <h6 class="name"><a href="{{ url('app-profile')}}">Nadila Adja</a></h6>
                                            <span class="fs-14 font-w400 text-wrap">Lorem ipsum dolor sit</span>
                                        </div>
                                    </div>													
                                    <div class="indox">
                                        <span class="fs-14 font-w400 text-wrap">12:45 PM</span>		
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-center border-0 px-3 pb-0">
                                <a href="javascriptvoid(0);" class="btn btn-block btn-primary light btn-rounded dlab-load-more" id="message" rel="{{ route('ajax_message_content')}}">View More</a>
                            </div>
                        </div>
                    </div>
                    <!--/column-->
                    <!--column-->
                    <div class="col-xl-12 ">
                        <div class="card tags bg-transparent mb-0">
                            <div class="card-header border-0 px-3 py-0">
                                <h2 class="heading">Current Foods Menu</h2>
                            </div>
                            <div class="card-body  p-3 py-1 ">	
                                <div class="card-body-inner food">
                                    <span class="dz-media">
                                        <img src="{{ asset('images/food/pic1.jpg') }}" alt="" class="avatar">
                                    </span>
                                    <div class="user-info">
                                        <h5 class="name"><a href="{{ url('app-profile')}}">Beef Steak with Fried Potato</a></h5>
                                        <span class="fs-14 font-w400 text-wrap">Lorem ipsum dolor sit</span>
                                    </div>
                                </div>	
                                <div class="card-body-inner food">
                                    <span class="dz-media">
                                        <img src="{{ asset('images/food/pic2.jpg') }}" alt="" class="avatar">
                                    </span>
                                    <div class="user-info">
                                        <h5 class="name"><a href="{{ url('app-profile')}}">Pancake with Honey</a></h5>
                                        <span class="fs-14 font-w400 text-wrap">Lorem ipsum dolor sit</span>
                                    </div>
                                </div>	
                                <div class="card-body-inner food">
                                    <span class="dz-media">
                                        <img src="{{ asset('images/food/pic3.jpg') }}" alt="" class="avatar">
                                    </span>
                                    <div class="user-info">
                                        <h5 class="name"><a href="{{ url('app-profile')}}">Japanese Beef Ramen</a></h5>
                                        <span class="fs-14 font-w400 text-wrap">Lorem ipsum dolor sit</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer pt-0 border-0 px-2">
                                <button class="btn btn-block btn-primary light btn-rounded  mb-3">View More</button>		
                            </div>
                        </div>	
                                
                    </div>
                    <!--column-->
                </div>
            </div>
            <div class="wallet-bar-close"></div>
        @endif
		<!--**********************************
            Content body start
        ***********************************-->
        <!--**********************************
            Content body start
        ***********************************-->
		@php
            $body_class = 'default-height'; 
            if($page == 'ui_button'){ $body_class = 'btn-page';} 
            if($page == 'ui_badge'){ $body_class = 'badge-demo';}
            if($page == 'file_manager'){ $body_class = 'mh-auto';}
            if($page == 'chat'){ $body_class = 'message-body mh-auto';}
            if($page == 'email_compose'){ $body_class = 'mh-auto';}
            if($page == 'email_inbox'){ $body_class = 'mh-auto';}
            if($page == 'email_read'){ $body_class = 'mh-auto';}
            if($page == 'invoice'){ $body_class = 'mh-auto';}
        @endphp
        <div class="content-body {{$body_class}}">
           @yield('content')
        </div>
		
        <!--**********************************
            Content body end
        ***********************************-->

        @stack('modal')
		<!--**********************************
			Footer start
		***********************************-->
        @if (!in_array($page,array('email_compose','email_inbox','email_read','file_manager','chat')))
            @include('elements.footer')
        @endif

	</div>

	
    <!--**********************************
        Main wrapper end
    ***********************************-->

<!--***********************************-->
	

		
	<!--**********************************
		Modal
	***********************************-->
    <!--**********************************
        Scripts
    ***********************************-->
    @if(!empty(config('dz.public.global.js.top')))
        @foreach(config('dz.public.global.js.top') as $script)
            <script src="{{ asset($script) }}" type="text/javascript"></script>
        @endforeach
    @endif
    @if(!empty(config('dz.public.pagelevel.js.'.$action)))
        @foreach(config('dz.public.pagelevel.js.'.$action) as $script)
            <script src="{{ asset($script) }}" type="text/javascript"></script>
        @endforeach
    @endif
    @if(!empty(config('dz.public.global.js.bottom')))
        @foreach(config('dz.public.global.js.bottom') as $script)
            <script src="{{ asset($script) }}" type="text/javascript"></script>
        @endforeach
    @endif
	
	@stack('scripts')
	
	
</body>
</html>