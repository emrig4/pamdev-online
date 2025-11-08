@extends('layouts.account')
@push('css')
    <link href="{{ asset('themes/airdgereaders/css/tag.css') }}" rel="stylesheet">
@endpush



@section('content')
    <!--// Main Section \\-->
    <div class="ereaders-main-section ereaders-counterfull">

        <!-- dashboard nav -->
        @include('partials.usermenu')

        <div class="container">
            <div class="row">                
    
               
                <div class="row">
                    <div class="col-md-6">
                        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                            @livewire('account::settings.update-picture')
                        @endif 
                    </div>

                    <div class="col-md-6">
                        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                            @livewire('account::settings.change-password')
                        @endif 
                    </div>
                     
                </div>

                <div class="row mt-10">
                    <div class="col-md-6">
                        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                            @livewire('account::settings.update-contact-info')
                        @endif 
                    </div>
                    <div class="col-md-6">
                        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                            @livewire('account::settings.update-personal-information')
                        @endif 
                    </div>
                </div>


                <div class="row mt-10">
                    <div class="col-md-6">
                        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                            @livewire('account::settings.update-bank-info')
                        @endif 
                    </div>
                </div>



            </div>
        </div>
    </div>
    <!--// Main Section \\-->
@endsection

@push('js')
    <script>
      $( function() {
        $( "#dob" ).datepicker();
      } );
    </script>
    <script type="text/javascript" src="{{ asset('themes/airdgereaders/js/subfields.js') }}"></script>
@endpush
