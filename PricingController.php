<?php

namespace App\Modules\Subscription\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Modules\Subscription\Models\Pricing;

class PricingController extends Controller
{
    public function index()
    {
        $basic = Pricing::where('slug', 'basic')->first();
        $standard = Pricing::where('slug', 'standard')->first();
        $pro = Pricing::where('slug', 'pro')->first();

        return view('admin.pricings.index', compact('basic', 'standard', 'pro'));
    }

    public function pay(Request $request)
    {
        $method = $request->query('method');
        $item = '';
        if ($method === 'paystack') {
            return view('payment::paystackpay', ['item' => $item]);
        }
    }

    public function store(Request $request)
    {
        //
    }

    public function show($slug)
    {
        $pricing = Pricing::where('slug', $slug)->firstOrFail();
        return view('admin.pricings.single', compact('pricing'));
    }

    public function update(Request $request, Pricing $pricing)
    {
        $pricing->update($request->all());
        return redirect()->back()->with('success', 'Pricing plan updated successfully!');
    }

    public function destroy($id)
    {
        //
    }
}
