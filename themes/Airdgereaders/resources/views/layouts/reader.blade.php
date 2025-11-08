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
		<link href="{{ theme_asset('css/style.css') }}" rel="stylesheet">
		<link href="{{ theme_asset('css/color.css') }}" rel="stylesheet">
		<link href="{{ theme_asset('css/responsive.css') }}" rel="stylesheet">
		<link href="{{ theme_asset('css/typography.css') }}" rel="stylesheet">

		<!-- theme -->
		<link href="{{ theme_asset('css/theme.css') }}" rel="stylesheet">
		@stack('css')

	</head>
	<body class="ereaders-sticky">
		<!--// Main Wrapper \\-->
		<div class="ereaders-main-wrapper">

		<!-- include header here -->
		@include('partials.readerheader', ['title' => $title])

		<!--// Main Content \\-->
		<div class="ereaders-main-content ereaders-content-padding">
			@yield('content')
		</div>
		<!--// Main Content \\-->


		<!-- mini footer start -->
		@include('partials.copyright')
		<!-- mini footer end -->

		<div class="clearfix"></div>

		</div>
		<!--// Main Wrapper \\-->

		 <!-- jQuery (necessary for JavaScript plugins) -->
		<!-- <script type="text/javascript" src="{{ theme_asset('js/jquery.js') }}"></script> -->
		<script type="text/javascript" src="{{ theme_asset('js/bootstrap.min.js') }}"></script>
		<!-- <script type="text/javascript" src="{{ theme_asset('js/functions.js') }}"></script> -->

		@stack('js')
		@livewireScripts
        <x:notify-messages />
        @notifyJs
        <!--Start of Tawk.to Script-->
        @php
            echo setting('tawk_widget');
        @endphp
        <!--End of Tawk.to Script-->
	</body>
</html>
