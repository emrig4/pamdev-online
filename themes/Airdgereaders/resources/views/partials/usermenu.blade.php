<!-- dashboard nav -->
<div class="col-md-12">
    <div id="style-4-scrollbar" class="ereaders-shop-filter md:py-5 px-0 overflow-x-scroll">
        <!-- Nav tabs -->
        <ul style="display: flex" class="nav-tabs pull-left" role="tablist">
            <li role="presentation" class="">
                <a href="{{route('account.index')}}" >
                    <i class="icon ereaders-list-interface-symbol"></i> 
                    <span>Dashboard</span>
                </a>
            </li>

           <!--  <li role="presentation" class="">
                <a href="{{ route('account.myworks') }}" >
                    <i class="icon ereaders-list-interface-symbol"></i> 
                    <span>My Works</span>
                </a>
            </li> -->
            <li role="presentation" class="">
                <a href="{{ route('account.mywallet') }}">
                    <i class="icon ereaders-list-interface-symbol"></i>
                    <span>Wallet</span>
                </a>
            </li>
           
            <li role="presentation" class="">
                <a href="{{ route('account.subscription') }}">
                    <i class="icon ereaders-3x3-grid"></i>
                    <span>Subscription</span>
                </a>
            </li> 

            <li role="presentation" class="">
                <a href="{{ route('account.favorites') }}">
                    <i class="icon ereaders-3x3-grid"></i>
                    <span>Favorites</span>
                </a>
            </li>
            

        </ul>
        
    </div>
</div>