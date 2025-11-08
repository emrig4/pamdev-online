<?php

namespace App\Modules\Account\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Modules\Wallet\Models\CreditWalletTransaction;
use App\Modules\Resource\Models\Resource;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use App\Modules\Resource\Models\ResourceAuthor;
use App\Modules\Account\Models\Account;


class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        // Get or create account for the authenticated user
        $account = auth()->user()->account;
        
        if (!$account) {
            // Create account if it doesn't exist
            $account = Account::create([
                'user_id' => auth()->id(),
                'status' => 1
            ]);
        }

        // Now get resources using the account
        $resources = $account->resources()->paginate(20);
        return view('account::index', ['resources' => $resources]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('account::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('account::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('account::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }


    /**
     * My works.
     * @return Renderable
     */
    public function myWorks()
{
    // 1. Get or create account for authenticated user
    $account = auth()->user()->account;
    
    if (!$account) {
        // Create account if doesn't exist
        $account = Account::create([
            'user_id' => auth()->id(),
            'status' => 1
        ]);
    }

    // 2. PAGINATION CONTROL: Get paginated resources (20 per page)
    $resources = $account->resources()->paginate(20);
    
    // 3. Get all resources for statistics (not paginated)
    $allUserResources = $account->resources()->get();
    $totalReads = $allUserResources->sum('read_count');
    $totalDownloads = $allUserResources->sum('download_count');
    $totalWorks = $allUserResources->count();
    $totalResourcesInSystem = Resource::count();
    
    // 4. Pass all data to view
    return view('account::myworks', [
        'resources' => $resources,           // ← PAGINATED DATA
        'totalReads' => $totalReads,         // For stats display
        'totalDownloads' => $totalDownloads, // For stats display
        'totalWorks' => $totalWorks,         // For stats display
        'totalResourcesInSystem' => $totalResourcesInSystem // For stats display
    ]);
}


    /**
     * My works.
     * @return Renderable
     */
    public function myWallet()
    {
        $wallet = auth()->user()->CreditWallet;
        if ($wallet) {
            $walletHistory = CreditWalletTransaction::where('credit_wallet_id', $wallet->id);
        } else {
            $walletHistory = collect(); // Empty collection if no wallet
        }
        return view('account::mywallet', compact('wallet','walletHistory'));
    }

    /**
     * myProfile.
     * @return Renderable
     */
    public function myProfile($username)
    {   
        
        $author = ResourceAuthor::where('username', $username)->firstOrFail();
        return view('account::profile', compact('author'));
    }

    /**
     * My works.
     * @return Renderable
     */
    public function creditWalletHistory()
    {
       if (request()->ajax()) {
            $wallet = auth()->user()->CreditWallet;
            if ($wallet) {
                $walletHistory = CreditWalletTransaction::where('credit_wallet_id', $wallet->id)->get();
            } else {
                $walletHistory = collect(); // Empty collection if no wallet
            }
            $data = collect($walletHistory);
            return Datatables::of($data)
                ->addColumn('action', function($row){    
                    $showUrl = route('admin.subscriptions.show', $row->id);
                    $cancelUrl = route('wallet.cancel-withdrawal', $row->id);

                    $btn = "<a href='$cancelUrl' style='padding: 2px' class='text-xs h-4 btn btn-danger'>Cancel</a>";
                    if($row->type == 'withdrawal' && $row->status == 'pending'){
                        return $btn;
                    }else{
                        return;
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        }else{
            $wallet = auth()->user()->CreditWallet;
            if ($wallet) {
                $walletHistory = CreditWalletTransaction::where('credit_wallet_id', $wallet->id);
            } else {
                $walletHistory = collect(); // Empty collection if no wallet
            }
            return view('account::mywallet', compact('wallet','walletHistory'));
        }
    }


    /**
     * My notifications.
     * @return Renderable
     */
    public function myNotifications()
    {
        $notifications = auth()->user()->notifications()->paginate(10);
        // dd($notifications);
        return view('account::notifications', compact('notifications'));
        
    }

    public function markNotificationAsRead($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            if ($notification->read_at) {
                $notification->markAsUnRead();
            }else{
                $notification->markAsRead();
            }
        }

        return redirect()->back();
    }


}