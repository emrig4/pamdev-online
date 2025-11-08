<!DOCTYPE html>
<html lang="en">
	<head>
		<meta name="google-site-verification" content="kxif4mNzcVplMsLcmLHjvyQV5XVbC6UPmpV3rYgMShk" />
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		@seoTags()
		@stack('meta')
		<title>Authoran - {{ (isset($title)) ? $title : '' }}</title>
		<link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.0.2/tailwind.min.css" rel="stylesheet">

        @notifyCss

		<!-- Css Files -->
		<link href="{{ theme_asset('css/bootstrap.css') }}" rel="stylesheet">
		<link href="{{ theme_asset('css/font-awesome.css') }}" rel="stylesheet">
		<link href="{{ theme_asset('css/flaticon.css') }}" rel="stylesheet">
		<link href="{{ theme_asset('css/slick-slider.css') }}" rel="stylesheet">
		<link href="{{ theme_asset('css/fancybox.css') }}" rel="stylesheet">
		<link href="{{ theme_asset('css/style.css') }}" rel="stylesheet">
		<link href="{{ theme_asset('css/color.css') }}" rel="stylesheet">
		<link href="{{ theme_asset('css/responsive.css') }}" rel="stylesheet">
		<link href="{{ theme_asset('css/typography.css') }}" rel="stylesheet">
		<link href="{{ theme_asset('css/scrollbar.css') }}" rel="stylesheet">
		<link href="{{ theme_asset('css/search-mega.css') }}" rel="stylesheet">
		<link href="{{ theme_asset('css/image-optimization.css') }}" rel="stylesheet">


		<!-- theme -->
		<link href="{{ theme_asset('css/theme.css') }}" rel="stylesheet">
		@stack('css')
		<script src="{{ theme_asset('js/image-optimizer.js') }}"></script>


        <style>
            .tawk-card-primary {
                background-color: #37a5d8 !important;
                color: #fff;
                color: var(--tawk-header-text-color);
            }
        </style>
        
	</head>
	<body class="ereaders-sticky">
		<!--// Main Wrapper \\-->
		<div class="ereaders-main-wrapper">

		<!-- include header here -->
		@include('partials.header')

		@yield('banner')

		@yield('breadcrumb')

		<!--// Main Content \\-->
		<div class="ereaders-main-content ereaders-content-padding">
			@yield('content')
		</div>
		<!--// Main Content \\-->


		<!-- Footer start -->
		@include('partials.footer')
		<!-- Footer end -->

		<div class="clearfix"></div>

		</div>
		<!--// Main Wrapper \\-->

		 <!-- jQuery (necessary for JavaScript plugins) -->
		<!-- <script type="text/javascript" src="{{ theme_asset('js/jquery.js') }}"></script> -->
		<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
		<!-- jquery ui -->
		<script type="text/javascript" src="{{ theme_asset('js/jquery-ui.js') }}"></script>


		<script type="text/javascript" src="{{ theme_asset('js/bootstrap.min.js') }}"></script>
		<script type="text/javascript" src="{{ theme_asset('js/slick.slider.min.js') }}"></script>
		<script type="text/javascript" src="{{ theme_asset('js/fancybox.pack.js') }}"></script>
		<script type="text/javascript" src="{{ theme_asset('js/isotope.min.js') }}"></script>
		<script type="text/javascript" src="{{ theme_asset('js/progressbar.js') }}"></script>
		<script type="text/javascript" src="{{ theme_asset('js/jquery.countdown.min.js') }}"></script>
		<script type="text/javascript" src="{{theme_asset('js/circle-chart.js') }}"></script>
		<script type="text/javascript" src="{{ theme_asset('js/numscroller.js') }}"></script>
		<script type="text/javascript" src="{{ theme_asset('js/functions.js') }}"></script>

		@stack('js')
        <x:notify-messages />
        @notifyJs


    <!--Start of Tawk.to Script-->
    @php
        echo setting('tawk_widget');
    @endphp
    <!--End of Tawk.to Script-->
	</body>
</html>
