@extends('layouts.public')
@push('meta')
    <meta name="description" content="Publish your research works on the largest academia library, gain recognigtion and also earn through our premium program."/>
    <meta property="title" content="Authoran.com, Digital Academic Resources, Research, Project, Thesis">
    <meta name="keywords" content="Authoran, Digital Academic Resources, Research, Project, Thesis">
    <meta property="og:title" content="Authoran.com, Digital Academic Resources, Research, Project, Thesis">
    <meta property="og:description" content="Publish your research works on the largest academia library, gain recognigtion and also earn through our premium program.">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
 <title>Authoran.com | publish and Gain recognition | Get cited and review</title>
 <meta name="description" content="Large online resource library where authors and academician meets. Publishing helps you establish your position as an expert in your field of knowledge..">
 <meta name="keywords" content="authors publication, authors project topics, research paper, journals, publish and earn">
@endpush
@push('css')
@endpush
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9522930547476630"
     crossorigin="anonymous"></script>
{{-- 
@section('banner')
    @include('partials.banner')
@endsection
--}}



@section('content')
    <!--// Main Section \\-->
    <div class="ereaders-main-section ereaders-counterfull">
        <div class="container">
            <div class="row">                
    
                <div class="col-md-12">
                    <div class="ereaders-counter">
                        <ul class="">
                            <li>
                                <div class="ereaders-counter-text">
                                    <i class="icon ereaders-document"></i>
                                    <div class="ereaders-scroller"><h2 class="numscroller" data-slno="1" data-min="0" data-max="" data-delay="" data-increment=""></h2> <span>Projects</span></div>
                                </div>
                            </li>
                            <li>
                                <div class="ereaders-counter-text">
                                    <i class="icon ereaders-book"></i>
                                    <div class="ereaders-scroller"><h2 class="numscroller" data-slno="1" data-min="0" data-max=" " data-delay=" " data-increment=" "></h2> <span>journals</span></div>
                                </div>
                            </li>
                            <li>
                                <div class="ereaders-counter-text">
                                    <i class="icon ereaders-download-content"></i>
                                    <div class="ereaders-scroller"><h2 class="numscroller" data-slno="1" data-min="0" data-max=" " data-delay=" " data-increment=" "> </h2> <span>Academic Materials</span></div>
                                </div>
                            </li>

                            <li>
                                <div class="ereaders-counter-text">
                                    <i class="icon ereaders-document"></i>
                                    <div class="ereaders-scroller"><h2 class="numscroller" data-slno="1" data-min="0" data-max=" " data-delay=" " data-increment=" "> </h2> <span>Books</span></div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!--// Main Section \\-->

    <!--// Main Section \\-->
    <div class="ereaders-main-section ereaders-work-learnfull">
        <div class="container">
            <div class="row">

                <div class="col-md-7">
                    <div class="ereaders-work-learn">
                        <h2>Learn <span>how to publish</span> and gain recognition from your published works</h2>
                        <p>Build your professional profile, Publish your papers to  reach thousands of scholars. Get cited and reviewed. </p>
                        <p>Access research works, Final year Project topics, thesis, dissertations, term papers, journals, assignments, novel, history and more.</p>
                        <a href="https://pamdev.online/resources/upload" class="ereaders-simple-btn ereaders-bgcolor">Get Started Now <i class="fa fa-angle-right"></i></a>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!--// Main Section \\-->

    <!--// Main Section services \\-->
    <div class="hidden ereaders-main-section ereaders-service-gridfull">
        <div class="container">
            <div class="row">

                <div class="col-md-12">
                    <div class="ereaders-fancy-title">
                        <h2>Our Featured Sevices</h2>
                        <div class="clearfix"></div>
                        <p>Large online resource library where authors and academician meets.</p>
                    </div>

                    <!-- include services grid here -->
                </div>

            </div>
        </div>
    </div>
    <!--// Main Section \\-->


    <!--// Main Section Top Fields \\-->
    <div class="ereaders-main-section ereaders-service-gridfull">
        <div class="container">
            <div class="row">

                <div class="col-md-12">
                    <div class="ereaders-fancy-title">
                        <h2> Featured Fields</h2>
                        <div class="clearfix"></div>
                        <p>Large online resource library where authors and academician meets.</p>
                    </div>

                    <!-- include services grid here -->
                    @include('resource.partials.featured_fields_grid')

                    <a href="https://pamdev.online/resources/fields" class="ereaders-simple-btn ereaders-bgcolor">Browse All Fields <i class="fa fa-angle-right text-white"></i></a>
                </div>

            </div>
        </div>
    </div>
    <!--// Main Section \\-->

   

    <!--// Main Section Blog \\-->
        
    <!--// Main Section \\-->

    <!--// Main Section \\-->
    <div class="ereaders-main-section">
        <div class="container">
            <div class="row">

                <aside class="col-md-6">
    <div class="ereaders-author-thumb">
        <img src="{{ theme_asset('images/default-project-cover.png') }}" 
             alt="Authoran" 
             loading="lazy" 
             decoding="async" 
             width="600" 
             height="400">
    </div>
</aside>

                <aside class="col-md-6">
                    <div class="ereaders-author-text">
                        <h2>About <span>Authoran</span></h2>
                        <h5>Authoran is digital academic resources where authors and academician meet. 
</h5>
                        <p>Authoran enable you to Publish your papers, Build a professional profile, become a  scholars, Get cited and review. This includes  access to Project topics, thesis, dissertations, term papers, journals, assignments,books e.t.c.  <a href="https://pamdev.online/about-us" class="ereaders-readmore-btn">Read more <i class="fa fa-angle-double-right"></i></a></p>
                        <ul class="ereaders-author-social">
                            <li><a href="https://web.facebook.com/Authoranlibrary" class="fa fa-facebook"></a></li>
                            
                        </ul>
                    </div>
                </aside>

            </div>
        </div>
    </div>
    <!--// Main Section \\-->

    <!--// Main Section \\-->
    <div class="ereaders-main-section ereaders-testimonialfull">
        <div class="container">
            <div class="row">

                <div class="col-md-12">
                    <div class="ereaders-testimonial">
                        <div class="ereaders-testimonial-wrap">
                            <div class="ereaders-fancy-title"><h2>What People say</h2></div>
                            <div class="ereaders-testimonial-slide">
                                <div class="ereaders-testimonial-slide-layer">
                                    <figure><img src="" alt=""></figure>
                                    <div class="ereaders-testimonial-text">
                                        <h4><a href="404.html">Jessica Mann</a></h4>
                                        <span>University of Abuja</span>
                                        <p>“Authoran enable me to access different resources which includes Project, thesis, dissertation, journal and other resources which I was able to compare and gather informations related to my topic.” </p>
                                    </div>
                                </div>
                                <div class="ereaders-testimonial-slide-layer">
                                    <figure><img src="" alt=""></figure>
                                    <div class="ereaders-testimonial-text">
                                        <h4><a href="404.html">Maria Okiptu</a></h4>
                                        <span>Federal Universiy of Technology, Minna</span>
                                        <p>“ With authoran I was able to write my final year project topic as it makes the process more easier, it gives me access to  projects from various departments. 
” </p>
                                    </div>
                                </div>
                                <div class="ereaders-testimonial-slide-layer">
                                    <figure><img src="" alt=""></figure>
                                    <div class="ereaders-testimonial-text">
                                        <h4><a href="404.html">JOSEPH KURE</a></h4>
                                        <span>Imo state Universiy</span>
                                        <p>“Would recommend authoran to others because  it was a good source to my research which make my work easier. 
” </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!--// Main Section \\-->

    <!--// Main Section \\-->
    <div class="ereaders-main-section ereaders-app-sectionfull">
        <div class="container">
            <div class="row">

                <div class="col-md-12">
                    <div class="ereaders-fancy-title">
                        <h2>Read It In All Devices</h2>
                        <div class="clearfix"></div>
                        <p>Large online resource library where authors and academician meets.</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="ereaders-app-text">
                        <h5>Publishing helps you establish your position as an expert in your field of knowledge. </h5>
                        <p>The solid body of the published work will help you advance your career as you will be subject to academic appointments and promotions, also helps you establish your position as an expert in your field of knowledge and preserve your work in the permanent records of research database.</p>
                        <p>Published works can contribute to the general understanding of  research questions. </p>
                        <div class="">
                            <a href="#" class="ereaders-fancy-btn flex"><i class="icon ereaders-apple-logo"></i> <span><small>GET IT ON</small><br> AppStore</span></a>
                            <a href="#" class="ereaders-fancy-btn flex"><i class="icon ereaders-play-store"></i> <span><small>GET IT ON</small><br> GooglePlay</span></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="ereaders-app-thumb"><img src="{{theme_asset('extra-images/app-thumb.jpg') }}" alt=""></div>
                </div>

            </div>
        </div>
    </div>
    <!--// Main Section \\-->

   
@endsection
