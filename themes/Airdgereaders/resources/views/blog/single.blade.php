@extends('layouts.public', ['title' => 'Browse Fields'])
@push('meta')
@endpush
@push('css')

        <style>
            .ereaders-thumb-text h1{
                color: black;
            }
            .ereaders-thumb-option li{
                color: black;
            }
            .ereaders-thumb-option li a, .ereaders-thumb-option li time {
                color: black;
            }
        </style>    
@endpush



@section('content')
    <div class="ereaders-main-content">

       
        <div class="ereaders-thumb-text mb-20">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 flex flex-col">
                        <h1 class="text-center text-black">{{$post->title}}</h1>
                        <ul class="ereaders-thumb-option mx-auto">
                            <li>Date: <time datetime="2008-02-14 20:00">{{$post->created_at->diffForHumans()}}</time></li>
                            <li>Posted By: <a href="#">{{ $post->author }}</a></li>
                            <li>Comments: <a href="404.html">{{ $comments->count() }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


       
        <!--// Main Section \\-->
        <div class="ereaders-main-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-9">
                        <!-- <h1 class='blog_title'>{{$post->title}}</h1> -->
                        <h5 class='blog_subtitle'></h5>
                            

                        <div class="ereaders-rich-editor">
                            <?=$post->image_tag("medium", false, 'd-inline float-right w-1/2 border border-white m-10'); ?>
                            <p class="blog_body_content">
                                {!! $post->post_body_output() !!}

                                {{--@if(config("binshopsblog.use_custom_view_files")  && $post->use_view_file)--}}
                                {{--                                // use a custom blade file for the output of those blog post--}}
                                {{--   @include("binshopsblog::partials.use_view_file")--}}
                                {{--@else--}}
                                {{--   {!! $post->post_body !!}        // unsafe, echoing the plain html/js--}}
                                {{--   {{ $post->post_body }}          // for safe escaping --}}
                                {{--@endif--}}
                            </p>
                        </div>
   
                        @includeWhen($categories,"blog.partials.blog_tags",['categories'=>$categories])


                        <h2 class="ereaders-section-heading">Related Blog Post</h2>
                        <div class="ereaders-blog ereaders-related-blog">
                            <ul class="row">
                                @foreach($related_posts as $post)
                                    <li class="col-md-4">
                                        <figure><a href="blog-detail.html"><img src="extra-images/related-blog-img1.jpg" alt=""></a></figure>
                                        <div class="ereaders-related-blog-text">
                                            <h5><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h5>
                                            <ul class="ereaders-blog-option">
                                                <li>{{ $post->created_at->diffForHumans() }}</li>
                                            </ul>
                                            
                                        </div>
                                    </li>
                                @endforeach
                               
                            </ul>
                        </div>

                        <!-- post comments here -->

                    </div>

                    <!--// Blog Sidebar \\-->
                    @include('blog.partials.blog_sidebar')
                    <!--// Sidebar \\-->

                </div>
            </div>
        </div>
        <!--// Main Section \\-->

    </div>



@endsection
