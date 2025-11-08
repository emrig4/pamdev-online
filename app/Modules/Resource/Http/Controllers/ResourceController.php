<?php

namespace App\Modules\Resource\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Modules\Resource\Models\Resource;
use App\Modules\Resource\Models\ResourceField;
use App\Modules\Resource\Http\Requests\ResourceStoreRequest;
use App\Modules\Resource\Http\Requests\ResourceUpdateRequest;

use Illuminate\Support\Str;
use App\Modules\File\Http\Traits\FileUploadTrait;
use App\Modules\File\Http\Traits\FileProcessTrait;
use DB;
use Bouncer;
use App\Modules\Resource\Models\ResourceAuthor;
use App\Models\User;
use Digikraaft\PaystackSubscription\Payment;

use Illuminate\Support\Facades\Storage;
use App\Modules\Wallet\Http\Traits\WalletTrait;


class ResourceController extends Controller
{       

    use FileUploadTrait;
    use FileProcessTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    /**
     * Show the form for uploading a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createUpload()
    {
        return view('resource.create.upload'); 

    }


    /**
     * Show the form for publishin a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createPublish()
    {
       
        return view('resource.create.publish');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Modules\Resource\Http\Requests\ResourceStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ResourceStoreRequest $request)
    {   
        try {

            $tempfile = $request->input('tmpfile_name');
            $authUser = auth()->user();

            $file = FileUploadTrait::transferTmpFile($tempfile, 's3');
            if($file){

                $params = $request->all();
                $params['page_count'] = $file->page_count;

                // $params['cover'] = $file->page_count;


                $resource = Resource::create($params);
                $entity = DB::table('entity_files')->insert([
                    'file_id' => $file->id,
                    'entity_type'=>'App\Modules\Resource\Models\Resource',
                    'entity_id'=> $resource->id,
                    'label'=>'main_file',
                    'created_at'=>$resource->created_at,
                    'updated_at'=>$resource->updated_at,
                ]);


                /*  STORE  AUTHOR/CO AUTHOR*/

               // save publisher user as lead author
                ResourceAuthor::create([
                    'fullname' => $authUser->first_name . ' ' .  $authUser->last_name,
                    'resource_id' => $resource->id,
                    'is_lead' => 1,
                    'username' => $authUser->username
                ]);

                // save coauthors
                $authors = explode(',',  $request->coauthors);
                $users = [];
                foreach($authors as $author){
                    $resourceAuthor = new ResourceAuthor;
                    $resourceAuthor->resource_id = $resource->id;
                    $resourceAuthor->fullname = $author;

                    $user = User::whereRaw("CONCAT(`first_name`, ' ', `last_name`) LIKE ?", ['%'.$author.'%'])
                            ->first();
                    if($user){
                        $resourceAuthor->username = $user->username;
                    }

                    $resourceAuthor->save();
                }
            }else{
                return redirect()->back()->with('notify.message', 'something went wrong');
            }


            

        } catch (Exception $e) {
            
        }


       // check if api or web
        if($request->header('Accept') == 'application/json'){
            return;
        }else{
            return redirect()->route('account.index');
        }
    }

    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $resource = Resource::where('slug', $slug)->first();
        if($resource){
            $resource->increment('view_count', 1);
            $mainFile = $resource->filterFiles('main_file')->first();
        }
        return view('resource::single', ['resource' => $resource, 'mainFile' => $mainFile ?? '']);

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cite($slug)
    {
        $resource = Resource::where('slug', $slug)->first();
        $authors =  $resource->authors()->pluck('fullname')->toArray();
        $ap6_authors = implode(' , ', $authors);
        $ap7_authors = implode(' & ', $authors);
        $mla8_authors = implode(' and ', $authors);
        return view('resource::citation', compact('resource', 'authors', 'ap7_authors', 'ap6_authors', 'mla8_authors' ));

    }


    /**
     * Read the specified resource file.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function read($slug)
    {

        $resource = Resource::where('slug', $slug)->first();
        $mainFile = $resource->filterFiles('main_file')->first() ;
        $sessionRead = \Session::get($slug);
        $resource->increment('read_count', 1);


        if($resource->price && $resource->currency && $resource->price > 0 && auth()->user() ){
            // if resource owner or admin
            if($resource->user_id == auth()->user()->id){
                 $sessionRead = \Session::put($slug, true);
            }
            
            if(!$sessionRead){

                // charge 150 rancs per unique_reads, 
                $ranc_per_onread = setting('ranc_per_onread');
                $resource_owner_percent_onread = (double) setting('resource_owner_percent_onread')/100 ; // 10%
                $authoran_user_id = setting('authoran_user_id');

                $walletBal = WalletTrait::subscriptionWalletBalance();
                if($walletBal > $ranc_per_onread) {

                   // return redirect()->back()->with('message', 'you dont sufficient credit in you current subscription bundle to read this resourcs, your balance is ???');
               
                    WalletTrait::debitSubscriptionWallet($ranc_per_onread);

                    // CREDIT RESOURCE OWNER
                    WalletTrait::creditWallet( ($ranc_per_onread * $resource_owner_percent_onread ), $resource->user_id, $resource->title); //90%

                    // CREDIT AUTHORAN 
                    WalletTrait::creditWallet($ranc_per_onread * (1 - $resource_owner_percent_onread) , $authoran_user_id, $resource->title ); //10%

                    $sessionRead = \Session::put($slug, true);
                    
                    notify()->success('You have been charged ' . $ranc_per_onread . ' Authoran credits');
                }else{
                    notify()->error('you dont sufficient credit in you current subscription bundle to read this resource, please buy extra credits ');
                    $sessionRead = \Session::put($slug, false);
                }
            }

            if($sessionRead){
                notify()->success('Reading ' . $resource->title);
            }

            return view('resource::viewer.index', ['resource' => $resource, 'mainFile' => $mainFile, 'sessionRead' => $sessionRead]);
        }else{
            return view('resource::viewer.index', ['resource' => $resource, 'mainFile' => $mainFile, 'sessionRead' => $sessionRead]);
        }


        

    }



    public function download(Request $request, $slug)
    {

        $resource = Resource::where('slug', $slug)->first();
        $mainFile = $resource->filterFiles('main_file')->first() ;
        $sessionRead = \Session::get($slug);

        $responseQuery = $request->query('response');

        // if resource owner or admin
        // if($resource->user_id == auth()->user()->id){
        //    return;
        // }
        
        
        if($resource->price && $resource->price > 0){

            // charge Ranc Per Download from admin settings
                      $ranc_per_download = setting('ranc_per_ondownload'); // Use admin setting instead of resource price  setting('ranc_per_ondownload'); // Use admin setting instead of resource price
            $resource_owner_percent_ondownload = (double)setting('resource_owner_percent_ondownload')/100;  //  90%
            $authoran_user_id = setting('authoran_user_id');

            // if paytack payment
            if($responseQuery){
                $response = json_decode($responseQuery);

                //verify the transaction is valid
                $transaction = Payment::hasValidTransaction($response->reference);
                if ($transaction) {

                     // CREDIT RESOURCE OWNER
                    WalletTrait::creditWallet( ($ranc_per_download * $resource_owner_percent_ondownload ), $resource->user_id, $resource->title); //settings

                     // CREDIT AUTHORAN 
                    WalletTrait::creditWallet($ranc_per_download * (1 - $resource_owner_percent_ondownload) , $authoran_user_id, $resource->title ); //settings

                    $sessionRead = \Session::put($slug, true);
                }else{
                    return;
                }
            }else{
                // wallet payment
                $walletBal = WalletTrait::subscriptionWalletBalance();
                if($walletBal > $ranc_per_download) {
               
                    WalletTrait::debitSubscriptionWallet($ranc_per_download);

                    // CREDIT RESOURCE OWNER
                    WalletTrait::creditWallet( ($ranc_per_download * $resource_owner_percent_ondownload ), $resource->user_id, $resource->title); //90%

                    // CREDIT AUTHORAN 
                    WalletTrait::creditWallet($ranc_per_download * (1 - $resource_owner_percent_ondownload) , $authoran_user_id, $resource->title ); //10%

                    $sessionRead = \Session::put($slug, true);
                }else{
                    notify()->error('you dont have sufficient credit in you current subscription bundle to download this resource, please buy extra credits ');
                    return redirect()->back();
                }
            }


            $resource->increment('download_count', 1);
            
            $file_name  = str_replace(array("/", "\\", ":", "*", "?", "«", "<", ">", "|"), "-", $mainFile->path );
            $headers = [
              'Content-Type'        => $mainFile->mime,            
              'Content-Disposition' => 'attachment; filename="'. $file_name .'"',
            ];

            return \Response::make(Storage::disk('s3')->get($mainFile->path), 200, $headers);
        }

        notify()->error('Something bad happened');
        return redirect()->back();

    }



    public function freeDownload(Request $request, $slug)
    {

        $resource = Resource::where('slug', $slug)->first();
        $mainFile = $resource->filterFiles('main_file')->first() ;
        $resource->increment('download_count', 1);

        $file_name  = str_replace(array("/", "\\", ":", "*", "?", "«", "<", ">", "|"), "-", $mainFile->path );
        $headers = [
          'Content-Type'        => $mainFile->mime,            
          'Content-Disposition' => 'attachment; filename="'. $file_name .'"',
        ];

        return \Response::make(Storage::disk('s3')->get($mainFile->path), 200, $headers);
        
        
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        
        $resource = Resource::where('slug', $slug)->first();
        $existingSubfields = explode(',', $resource->sub_fields);
        // dd($existingSubfields);
        $existingCoauthors = ResourceAuthor::where('resource_id', $resource->id)
                                            ->where('is_lead', false)->get();
        $mainFile = $resource->filterFiles('main_file')->first() ;
        // dd($resource);
        return view('resource.edit', [
            'resource' => $resource, 
            'mainFile' => $mainFile, 
            'existingSubfields' => $existingSubfields,
            'existingCoauthors' => $existingCoauthors
        ]);
        // dd( $mainFile->url() );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Modules\Resource\Http\Requests\ResourceUpdateRequest  $request
     * @param  int  $slug
     * @return \Illuminate\Http\Response
     */
    public function update(ResourceUpdateRequest $request,  $slug)
    {

        try {
            $authUser = auth()->user();
            $resource = Resource::where('slug', $slug)->first();

            $params = $request->all();
            if($params['currency'] == ''){
                $params['price'] = null;
            }
            $resource->update( $params );

            // dd($resource);
    
            // save coauthors
            $authors = explode(',',  $request->coauthors);
            $users = [];
            foreach($authors as $author){
                $resourceAuthor = new ResourceAuthor;
                $resourceAuthor->resource_id = $resource->id;
                $resourceAuthor->fullname = $author;

                $user = User::whereRaw("CONCAT(`first_name`, ' ', `last_name`) LIKE ?", ['%'.$author.'%'])
                        ->first();
                if($user){
                    $resourceAuthor->username = $user->username;
                }

                $resourceAuthor->save();
            }
    
        } catch (Exception $e) {
            
        }


       // check if api or web
        if($request->header('Accept') == 'application/json'){
            return;
        }else{
            return redirect()->route('account.myworks');
        }
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function search(Request $request){
        $field = $request->query('field');;
        $search = $request->query('search');
        $subfield = $request->query('subfield');
        $type = $request->query('type');

        $query = Resource::where(function($q)use($search, $subfield, $type, $field){
            if($search){
                $q->where('title', 'like', '%' . $search .'%');
            }
            if($type){
                $q->where('type', $type);
                
            }
            if($subfield){
                $q->whereIn('sub_fields',  [$subfield]);
            }
            

           $q->where('field', $field);
        });

        $resources =  $query->get();

        $field = ResourceField::where('slug', $field)->first();

        // dd($field);
            
        return view('resource.search', ['resources' => $resources, 'field' => $field]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( $slug)
    {       
        $resource = Resource::where('slug', $slug)->first();
        if($resource->user_id != auth()->user()->id ){
            notify()->success('Denied');
            return redirect()->route('account.myworks');
        }

        $resource->reports()->delete();
        $resource->delete();
        notify()->success('Deleted');
        return redirect()->route('account.myworks');
    }
}
