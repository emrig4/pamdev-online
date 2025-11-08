<div class="glide mt-5" id="carousel-style-1">
  <div class="glide__track" data-glide-el="track">
      <div class="glide__slides">
          <div class="glide__slide">
              <div class="border border-gray-300 rounded-lg px-4 h-full py-8 text-center">
                  <span class="text-primary text-5xl leading-none la la-sun"></span>
                  <p class="mt-2">Resources</p>
                  <div class="text-primary mt-5 text-3xl leading-none">{{ $resourceCounts }}</div>
                  <!-- <div class="flex justify-between bg-gray-100 p-4 rounded mt-5">
                    <span class="text-xs block"><span class="font-bold">Request:</span> 16</span>
                    <span class="text-xs block"><span class="font-bold">Published:</span> 6</span>
                  </div> -->
              </div>
          </div>
          <div class="glide__slide">
              <div class="border border-gray-300 rounded-lg px-4 h-full py-8 text-center">
                  <span class="text-primary text-5xl leading-none la la-cloud"></span>
                  <p class="mt-2">Subcribers</p>
                  <div class="text-primary mt-5 text-3xl leading-none">{{ $subscriptionCount }}</div>
                  <!-- <div class="flex justify-between bg-gray-100 p-4 rounded mt-5">
                    <span class="text-xs block"><span class="font-bold">Request:</span> 16</span>
                    <span class="text-xs block"><span class="font-bold">Approved:</span> 6</span>
                  </div> -->
              </div>
          </div>
          <div class="glide__slide">
              <div class="border border-gray-300 rounded-lg px-4 py-8 text-center">
                  <span class="text-primary text-5xl leading-none la la-layer-group"></span>
                  <p class="mt-2">Wallet Volume (RNC)</p>
                  <div class="text-primary mt-5 text-3xl leading-none">{{ $creditWalletVolume }}</div>
                  <!-- <div class="flex justify-between bg-gray-100 p-4 rounded mt-5">
                    <span class="text-xs block"><span class="font-bold">Credit:</span> 2906</span>
                    <span class="text-xs block"><span class="font-bold">Debit:</span> 6890</span>
                  </div> -->
              </div>
          </div>

          <div class="glide__slide">
              <div class="border border-gray-300 rounded-lg px-4 py-8 text-center">
                  <span class="text-primary text-5xl leading-none la la-layer-group"></span>
                  <p class="mt-2">Users</p>
                  <div class="text-primary mt-5 text-3xl leading-none">{{ $usersCount }}</div>
                  <!-- <div class="flex justify-between bg-gray-100 p-4 rounded mt-5">
                    <span class="text-xs block"><span class="font-bold">Active:</span>16</span>
                    <span class="text-xs block"><span class="font-bold">Inactive:</span>10</span>
                  </div> -->
              </div>
          </div>

          <div class="glide__slide">
              <div class="border border-gray-300 rounded-lg px-4 py-8 text-center">
                  <span class="text-primary text-5xl leading-none la la-cloud"></span>
                  <p class="mt-2">Revenue (RNC)</p>
                  <div class="text-primary mt-5 text-3xl leading-none">{{ $revenueAmount }}</div>
                  <!-- <div class="flex justify-between bg-gray-100 p-4 rounded mt-5">
                    <span class="text-xs block"><span class="font-bold">Subscription:</span> 10000</span>
                    <span class="text-xs block"><span class="font-bold">Purchases:</span> 6000</span>
                  </div> -->
              </div>
          </div>

          <div class="glide__slide">
              <div class="border border-gray-300 rounded-lg px-4 py-8 text-center">
                  <span class="text-primary text-5xl leading-none la la-cloud"></span>
                  <p class="mt-2">Pending Withrawals</p>
                  <div class="text-primary mt-5 text-3xl leading-none">{{$withdrawalCount}}</div>
                  <!-- <div class="flex justify-between bg-gray-100 p-4 rounded mt-5">
                    <span class="text-xs block"><span class="font-bold">Withdrawals:</span> 6</span>
                    <span class="text-xs block"><span class="font-bold">Deposits:</span>10</span>
                  </div> -->
              </div>
          </div>

          <div class="glide__slide">
              <div class="border border-gray-300 rounded-lg px-4 py-8 text-center">
                  <span class="text-primary text-5xl leading-none la la-cloud"></span>
                  <p class="mt-2">Withdrawal Volume (RNC)</p>
                  <div class="text-primary mt-5 text-3xl leading-none">{{ $walletWithdrawalVolume }}</div>
                  <!-- <div class="flex justify-between bg-gray-100 p-4 rounded mt-5">
                    <span class="text-xs block"><span class="font-bold">Request:</span> 16</span>
                    <span class="text-xs block"><span class="font-bold">Approved:</span> 6</span>
                  </div> -->
              </div>
          </div>
      </div>
  </div>
  <div class="glide__bullets" data-glide-el="controls[nav]">
      <button class="glide__bullet" data-glide-dir="=0"></button>
      <button class="glide__bullet" data-glide-dir="=1"></button>
      <button class="glide__bullet" data-glide-dir="=2"></button>
  </div>
  <div class="glide__arrows" data-glide-el="controls">
      <button class="glide__arrow glide__arrow--left" data-glide-dir="<"><span
              class="la la-arrow-left"></span></button>
      <button class="glide__arrow glide__arrow--right" data-glide-dir=">"><span
              class="la la-arrow-right"></span></button>
  </div>
</div>
