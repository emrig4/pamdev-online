@extends('layouts.public', ['title' => 'Browse Fields'])
@push('meta')
    <meta name="description" content="Publish your research works on the largest academia library, gain recognigtion and also earn through our premium program."/>
    <meta property="title" content="Authoran.com, Digital Academic Resources, Research, Project, Thesis">
    <meta name="keywords" content="Authoran, Digital Academic Resources, Research, Project, Thesis">
    <meta property="og:title" content="Authoran.com, Digital Academic Resources, Research, Project, Thesis">
    <meta property="og:description" content="Publish your research works on the largest academia library, gain recognigtion and also earn through our premium program.">
@endpush
@push('css')
@endpush

@section('content')
<!--// Main Section \\-->
<div class="ereaders-main-section ereaders-counterfull">
    <div class="container" style="width: 100%">

        <div class="row">

            <div class="col-md-8">
                <div class="ereaders-about-us">
                    <h2><span class="ereaders-color">ABOUT US</span></h2>
                    <p>Authoran enable you to Publish your research papers, Build a professional profile, Get cited and reviewed by thousands of scholars. This includes  access to  Project topics, thesis, dissertations, term papers, journals, assignments,books e.t.c. as our aim is to simplify research activities and the entire process of obtaining information for research  by providing reference tools use for quick and more comprehensive understanding of a research work.</p>
                    <p>
                        <h2><span class="ereaders-color"> BENEFITS</span></h2>
                        <ol>
                           <li>
                                Sharing the information  you find helps other researchers to proceed with their work, based on the  knowledge that exists in your field. .
                           </li>

                            <li>Published works can contribute to the general understanding of  research questions.</li>  

                            <li>The solid body of the published work will help you advance your career as you will be subject to academic appointments and promotions. </li>

                            <li>Publishing helps you establish your position as an expert in your field of knowledge. </li>

                            <li>The act of putting your research to paper will help you clarify your goals for the research, will help you in reviewing and interpreting your own data and force you to compare your work with that of others.</li>

                            <li>Publication helps to preserve your work in the permanent records of research database.</li>

                        </ol>
                    </p>
                    <a href="{{ route('resources.create.upload') }}" class="ereaders-simple-btn ereaders-bgcolor">Get Started Now <i class="fa fa-angle-right"></i></a>
                </div>
            </div>
            <div class="col-md-4">
                <figure class="ereaders-about-thumb">
                    <!-- <img src="{{ theme_asset('extra-images/about-thumb-img2.jpg') }}" alt="" class="one"> -->
                    <img src="{{ theme_asset('extra-images/about-thumb-img3.jpg') }}"  alt="" class="two">
                    <!-- <img src="{{ theme_asset('extra-images/about-thumb-img4.jpg') }}" alt="" class="three"> -->
                    <img src="{{ theme_asset('extra-images/about-thumb-img5.jpg') }}" style="top: 30%" alt="" class="four">
                </figure>
            </div>


             <div class="col-md-12">
                    <div class="ereaders-counter ereaders-about-counter">
                        <ul class="">
                            <li>
                                <div class="ereaders-counter-text">
                                    <i class="icon ereaders-document"></i>
                                    <div class="ereaders-scroller"><h2 class="numscroller" data-slno="1" data-min="0" data-max=" " data-delay="10" data-increment="9">1200</h2> <span>Projects</span></div>
                                </div>
                            </li>
                            <li>
                                <div class="ereaders-counter-text">
                                    <i class="icon ereaders-book"></i>
                                    <div class="ereaders-scroller"><h2 class="numscroller" data-slno="1" data-min="0" data-max=" " data-delay="10" data-increment="20">4500</h2> <span>Books</span></div>
                                </div>
                            </li>
                            <li>
                                <div class="ereaders-counter-text">
                                    <i class="icon ereaders-download-content"></i>
                                    <div class="ereaders-scroller"><h2 class="numscroller" data-slno="1" data-min="0" data-max=" " data-delay="10" data-increment="70">13550</h2> <span>Academic Materials</span></div>
                                </div>
                            </li>

                            <li>
                                <div class="ereaders-counter-text">
                                    <i class="icon ereaders-document"></i>
                                    <div class="ereaders-scroller"><h2 class="numscroller" data-slno="1" data-min="0" data-max=" " data-delay="10" data-increment="70">130</h2> <span>Articles</span></div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

        </div>

    </div>
</div>
<!--// Main Section \\-->
@endsection
