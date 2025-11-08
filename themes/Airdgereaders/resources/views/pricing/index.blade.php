@extends('layouts.public', ['title' => 'Browse Fields'])
@push('meta')
@endpush
@push('css')
@endpush



@section('content')
    <!--// Main Section \\-->
    <div class="ereaders-main-section ereaders-counterfull">
        <div class="container" style="width: 100%">
            <div class="row">                
                <div class="col-md-12">
                     @include('partials.fancy_title', ['title' => 'CREDIT TOP-UP', 'description' => 'credit your wallet to unlock limit and contunue read thousands of research papers from different authors.'])
                </div>


                <div class="col-md-12">
                    <div class="ereaders-priceplan">
                        <ul>
                            <li>
                                <div class="ereaders-priceplan-wrap">
                                    <h5 class="ereaders-bgcolor">{{$basic->title}}</h5>
                                    <div class="ereaders-priceplan-heading">
                                        <h2>
                                            <small class="block">{{ $basic->currency() }}</small>
                                            {{ $basic->price() }}
                                            <!-- <span>{{ $basic->price() }}<br><small>month</small></span> -->
                                        </h2>
                                    </div>
                                    <div class="ereaders-priceplan-list">
                                        <ul>
                                            @foreach($basic->features() as $feature)
                                                <li>{{ $feature }}</li>
                                            @endforeach
                                        </ul>
                                        <a href="{{route('pricings.show', $basic->slug)}}" class="ereaders-simple-btn ereaders-bgcolor">BUY CREDIT NOW</a>
                                    </div>
                                </div>
                            </li>
                            <li class="active">
                                <div class="ereaders-priceplan-wrap">
                                    <h5 class="ereaders-bgcolor">{{$standard->title}}</h5>
                                    <div class="ereaders-priceplan-heading">
                                        <h2>
                                            <small class="block">{{ $standard->currency() }}</small>
                                            {{ $standard->price() }}
                                            <!-- <span>{{ $standard->price() }}<br><small>month</small></span> -->
                                        </h2>
                                    </div>
                                    <div class="ereaders-priceplan-list">
                                        <ul>
                                            @foreach($standard->features() as $feature)
                                                <li>{{ $feature }}</li>
                                            @endforeach
                                        </ul>
                                        <a href="{{route('pricings.show', $standard->slug)}}" class="ereaders-simple-btn ereaders-bgcolor">BUY CREDIT NOW</a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="ereaders-priceplan-wrap">
                                    <h5 class="ereaders-bgcolor">{{$pro->title}}</h5>
                                    <div class="ereaders-priceplan-heading">
                                        <h2>
                                            <small class="block">{{ $basic->currency() }}</small>
                                            {{ $pro->price() }}
                                            <!-- <span>{{ $pro->price() }}<br><small>month</small></span> -->
                                        </h2>
                                    </div>
                                    <div class="ereaders-priceplan-list">
                                        <ul>
                                             @foreach($pro->features() as $feature)
                                                <li>{{ $feature }}</li>
                                            @endforeach
                                        </ul>
                                        <a href="{{route('pricings.show', $pro->slug)}}" class="ereaders-simple-btn ereaders-bgcolor">BUY CREDIT NOW</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div> 
                </div>
                <div class="col-md-12">
                     @include('partials.fancy_title', ['title' => 'HOW IT WORK', 'description' => 'Your wallet remain zero(0) until you buy credit to unlock and continue reading thousands of complete research works which includes final year projects, thesis, dissertation, journals, books and more...the unit will be credited to your wallet in your dashboard automatically once transaction is successful.'])
                </div>
            </div>
        </div>
    </div>
    <!--// Main Section \\-->
@endsection
