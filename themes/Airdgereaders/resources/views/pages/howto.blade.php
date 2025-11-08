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
            <div class="col-md-12">
                <div class="ereaders-fancy-title">
                    <h2>Quick Guides</h2>
                    <div class="clearfix"></div>
                    <p> Access to academic resources which includes final year Projects, thesis, dissertation, journal, books etc.
</p>
                </div>
            </div>

            <aside class="col-md-4">
                <div class="ereaders-faq-tabs">
                    <h2 class="ereaders-widget-title">CATEGORY</h2>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class=""><a href="#home" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="false">Students/Researchers</a></li>
                        <li role="presentation" class=""><a href="#subscription" aria-controls="subscription" role="tab" data-toggle="tab" aria-expanded="false">Authors/publishers</a></li>
                    </ul>
                </div>
            </aside>

            <aside class="col-md-8">
                <!-- Tab panes -->
                <div class="tab-content">

                    <div role="tabpanel" class="tab-pane active" id="home">
                        <ul>
                            <p>STUDENT/RESEARCHERS</p>
                            <li><a href="https://pamdev.online/login">Login</a> Or <a href="https://pamdev.online/register">Register</a> on pamdev.online</li>
                            <li>Read and download unlimited number of <a href="https://pamdev.online/project-topics-materials">research materials from different authors </a>  which includes <a href="https://pamdev.online/resources/types/project">projects, </a> <a href="https://pamdev.online/resources/types/journal">journals,</a> <a href="https://pamdev.online/resources/types/book">books, </a> <a href="https://pamdev.online/resources/types/thesis">thesis/dissertation, </a> seminar, essay etc..</li>
                            <li>To read and download more research materials, buy credit  to unlock read limit, <a href="https://pamdev.online/pricings">view credit units.</a></li>
                            <p>QUICK SEARCH AND GET RESULT</p>
                            <li>At the top click on search icon</li>
                            <li>Enter your keywords ( A word/pharse that will fetch results of articles that contain the information you want, example: crisis, productivity, advertising, corruption  etc. )</li>
                            <li>select the type of result you want by clicking the dropdown menu options, it could be you want results for project, thesis, dissertation, journal etc OR Sellect "all resource types" for maximum search results</li>
                            <li>click on search button</li>
                            <li>Read through the search results</li>
                            <li>Access and download </li>

                        </ul>
                    </div>


                    <div role="tabpanel" class="tab-pane" id="subscription">
                        <ul>
                            <li><a href="https://projects.pamdev.online/login">Login</a> Or <a href="https://projects.pamdev.online/register">Register</a> on pamdev.online</li>
                            <li>Click upload icon</li>
                            <li>Select your ebook in PDF and MS word format only</li>
                            <li>Upload and enter your the details.</li>
                            <li>Next is to specify preview number of pages </li>
                            <li>For full read dont specify preview number of pages, leave it blank</li>
                            <li>Click on publish</li>
                            <li><a href="https://pamdev.online/faq">See frequently asked questions and answers for more details</a></li>

                            
                        </ul>
                    </div>    
                </div>
            </aside>

        </div>

    </div>
</div>
 <div class="col-md-12">
                    <div class="ereaders-counter ereaders-about-counter">
                        <ul class="">
                            <li>
                                <div class="ereaders-counter-text">
                                    <i class="icon ereaders-document"></i>
                                    <div class="ereaders-scroller"><h2 class="numscroller" data-slno="1" data-min="0" data-max="" data-delay="10" data-increment="9">1200</h2> <span>Projects</span></div>
                                </div>
                            </li>
                            <li>
                                <div class="ereaders-counter-text">
                                    <i class="icon ereaders-book"></i>
                                    <div class="ereaders-scroller"><h2 class="numscroller" data-slno="1" data-min="0" data-max="" data-delay="10" data-increment="20">4500</h2> <span>Books</span></div>
                                </div>
                            </li>
                            <li>
                                <div class="ereaders-counter-text">
                                    <i class="icon ereaders-download-content"></i>
                                    <div class="ereaders-scroller"><h2 class="numscroller" data-slno="1" data-min="0" data-max="" data-delay="10" data-increment="70">13550</h2> <span>Academic Materials</span></div>
                                </div>
                            </li>

                            <li>
                                <div class="ereaders-counter-text">
                                    <i class="icon ereaders-document"></i>
                                    <div class="ereaders-scroller"><h2 class="numscroller" data-slno="1" data-min="0" data-max="" data-delay="10" data-increment="70">130</h2> <span>Articles</span></div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
<!--// Main Section \\-->
@endsection
