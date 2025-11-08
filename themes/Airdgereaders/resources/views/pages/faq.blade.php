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
                    <h2>Frequently Ask Question</h2>
                    <div class="clearfix"></div>
                    <p>Browse through our frequently asked questions</p>
                </div>
            </div>

            <aside class="col-md-4">
                <div class="ereaders-faq-tabs">
                    <h2 class="ereaders-widget-title">Category</h2>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class=""><a href="#home" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="false">General</a></li>
                     </ul>
                </div>
            </aside>

            <aside class="col-md-8">
                <!-- Tab panes -->
                <div class="tab-content">


                    <div role="tabpanel" class="tab-pane active" id="home">
                        <div class="panel-group ereaders-faq-accordion" id="accordiontwo" role="tablist" aria-multiselectable="true">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingOne">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordiontwo" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                             How do I download or unlock preview limit of a reseach work? 
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                                    <div class="panel-body">
                                        <p>You are unable to download or continue reading because the units in your wallet is zero (0).</p>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingTwo">
                                    <h4 class="panel-title">
                                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordiontwo" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            How do I top-up credit unit into my wallet?
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                    <div class="panel-body">
                                        <p>Visit below link to login and follow the instructions. The credit will be remitted to your wallet once transaction is successful. <b>link:&nbsp;<a href="https://pamdev.online/pricings">Top-credit</b></a></p>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingThree">
                                    <h4 class="panel-title">
                                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordiontwo" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                           How do i confirm the amount in my wallet? 
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                                    <div class="panel-body">
                                        <p>The wallet is located in your dashboard or click on the drop down link at the top of your profile to easily locate your wallet.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingFour">
                                    <h4 class="panel-title">
                                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordiontwo" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                          What's is the duration of my credit unit? 
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
                                    <div class="panel-body">
                                        <p>The duration is 30 days which is approximately one month starting from the day your wallet was credited.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    
                    </div>    
                </div>
            </aside>

        </div>

    </div>
</div>
<!--// Main Section \\-->
@endsection
