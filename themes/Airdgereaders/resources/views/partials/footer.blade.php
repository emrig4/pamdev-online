<!--// Footer \\-->

    <footer id="ereaders-footer" class="ereaders-footer-one">    
        <!--// Footer Widget \\-->
        <div class="ereaders-footer-widget">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="ereaders-footer-newslatter flex flex-col items-center">
                            <h2>Subscribe to Our Newsletter</h2>
                            <p>Sign up to our newsletter, so you can be the first to find out the latest resources and tips on authoran.</p>
                            <form action="#">
                                <input value="Enter Your Email Address" onblur="if(this.value == '') { this.value ='Enter Your Email Address'; }" onfocus="if(this.value =='Enter Your Email Address') { this.value = ''; }" tabindex="0" type="email">
                                <input type="submit" value="Subscribe">
                            </form>
                            <!-- <a href="#" class="ereaders-footer-logo"><img src="{{ asset('themes/airdgereaders/images/footer-logo.png') }}" alt=""></a> -->
                        </div>
                    </div>
                </div>


             
                <div class="row p-2" style="">
                    <div class="col-md-4 pt-5">
                        <div class="footer-widget">
                            <h4 class="footer-widget-title">Resources</h4>
                            <ul class="footer-menu list-style">
                                <li><a href="https://pamdev.online/project-topics-materials" class="">Browse projects </a></li>
                                <li><a href="https://pamdev.online/resources/types/book" >Browse Books </a></li>
                                <li><a href="https://pamdev.online/resources/types/journal" >Browse journals</a></li>
                                <li><a href="https://pamdev.online/resources/types/thesis" >Browse thesis</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-8 pt-5 flex justify-between">
                        <div class="">
                            <div class="footer-widget">
                                <h4 class="footer-widget-title">Company</h4>
                                <ul class="footer-menu list-style">
                                    <li><a href="https://pamdev.online/about-us" class=""> About </a></li>
                                    <li><a href="https://pamdev.online/how-it-works" class=""> how it works </a></li>
                                    <li><a href="https://pamdev.online/faq" class=""> FAQ</a></li>
                                    <li><a href="https://pamdev.online/pricings" class=""> Pricing</a></li>

                                </ul>
                            </div>
                        </div>
                        <div class="">
                            <div class="footer-widget">
                                <h4 class="footer-widget-title">Quick-links</h4>
                                <ul class="footer-menu list-style">
                                    <li><a href="https://pamdev.online/" class=""> Home </a></li>
                                    <li><a href="https://pamdev.online/register" > join </a></li>
                                    <li><a href="https://pamdev.online/login" > login </a></li>
                                    <li><a href="https://pamdev.online/logout" > logout </a></li>


                                </ul>
                            </div>
                        </div>
                        <div class="">
                            <div class="footer-widget">
                                <h4 class="footer-widget-title">Tools</h4>
                                <ul class="footer-menu list-style">
                                    <li><a href="https://pamdev.online/blog" > Blog </a></li>
                                    <li><a href="#" class=""> Plagiarism </a></li>
                                    <li><a href="#" > Paraphrase </a></li>
                                    <li><a href="#" > Re-write </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
        <!--// Footer Widget \\-->
  
        @include('partials.copyright')
    </footer>
    <!--// Footer \\-->