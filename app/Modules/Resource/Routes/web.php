<?php
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Modules\File\Models\TemporaryFile;
use App\Modules\Resource\Http\Controllers\ResourceController;
use App\Modules\Resource\Http\Controllers\ResourceSubFieldController;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\MountManager;
use Illuminate\Http\File;
use App\Modules\File\Models\File as Files;
use App\Modules\Resource\Models\Resource;
use App\Modules\Resource\Models\ResourceSubField;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your module. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::group(['prefix' => 'resources', 'middleware' => ['web'] ], function () {

	// upload resource file
    Route::get('/upload','ResourceController@createUpload' )
        ->name('resources.create.upload')
        ->middleware('auth');

    // create resource
    Route::get('/publish', 'ResourceController@createPublish')
        ->name('resources.create.publish')
        ->middleware('fileuploaded','auth');

    // store resource
    Route::post('/publish',  'ResourceController@store')
        ->name('resources.store.publish')
        ->middleware('auth');

    // resource index
    Route::get('/', 'ResourceFieldController@index')
        ->name('resources.index');


    // fields index
    Route::get('/fields', 'ResourceFieldController@index')
        ->name('resources.fields');

    // single field
    Route::get('/fields/{slug}', 'ResourceFieldController@show')
        ->name('resources.fields.show');



    // single resource type
    Route::get('/types/{slug}', 'ResourceTypeController@show')
        ->name('resources.types.show');



    // subfields index (topics)
    Route::get('/topics', 'ResourceSubFieldController@index')
        ->name('resources.topics');

    // single subfield (topic)
    Route::get('/topics/{slug}', 'ResourceSubFieldController@show')
        ->name('resources.topics.show');

    // resource search
    Route::post('/search', 'ResourceController@search')
        ->name('resources.search');
    
    // search results
    Route::get('/search', 'ResourceController@search')->name('resources.searches');

    // single resource
    Route::get('/{slug}', 'ResourceController@show')
        ->name('resources.show');
        // ->middleware('auth');


    // single resource citation
    Route::get('/{slug}/cite', 'ResourceController@cite')
        ->name('resources.cite')
        ->middleware('auth');

    // edit single resource page
    Route::get('/{slug}/edit', 'ResourceController@edit')
        ->name('resources.edit')
        ->middleware('auth');


    // edit single resource pagec
    Route::post('/{slug}/edit', 'ResourceController@update')
        ->name('resources.update')
        ->middleware('auth');

    Route::get('/{slug}/delete',  'ResourceController@destroy')
            ->name('resources.delete')
            ->middleware('auth');



    // reader
    Route::get('/{slug}/read', 'ResourceController@read')
        ->name('resources.read');
         // ->middleware('auth');


    // download
    Route::get('/{slug}/download', 'ResourceController@download')
        ->name('resources.download')
         ->middleware('auth');

    // download
    Route::get('/{slug}/freedownload', 'ResourceController@freeDownload')
        ->name('resources.freedownload');
         // ->middleware('auth');


    // review
    Route::resource('/{slug}/reviews', 'ResourceReviewController')
        ->middleware('auth');

    // report
    Route::resource('/{resource}/reports', 'ResourceReportController')
        ->middleware('auth');

});


Route::group(['prefix' => 'subfields'], function () {
    // get subfields for ajax calls
    Route::get('/', function(){
        if (request()->has('field')) {
            $query = request()->input('field');
             return ResourceSubField::where('parent_field', $query )->get();
        }
        return  ResourceSubField::all();
    })->name('subfields');
});



Route::group(['prefix' => 'authors'], function () {
    // get subfields for ajax calls
    Route::get('/', function(){
        return  \App\Models\User::all();
    })->name('authors');
});


Route::get('/test', function(){
     $resourc = Resource::all();
            return $resourc;
});


Route::get('/docs', function(){
    $resource = Resource::first();
    return view('resource.viewer.dochtmlviewer', ['resource' => $resource]);
});


Route::get('/project-topics-materials', 'ResourceTypeController@projectTopicType')->name('resources.project.type');




Route::namespace('Admin')->prefix('admin')->middleware('role:sudo|admin|publisher')->group(function() {

    Route::group([ 'prefix' => 'resources',  'middleware' => ['auth', 'web', 'permission'] ], function() {

        // upload resource file
        Route::get('/upload','ResourceController@createUpload' )
            ->name('admin.resources.create.upload')
            ->middleware('auth');

        // create resource
        Route::get('/publish', 'ResourceController@createPublish')
            ->name('admin.resources.create.publish')
            ->middleware('fileuploaded','auth');

        // store resource
        Route::post('/publish',  'ResourceController@store')
            ->name('resources.store.publish')
            ->middleware('auth');


         // store resource
        Route::post('/publish',  'ResourceController@resourcePublishImport')
            ->name('admin.resources.import')
            ->middleware('auth');


        // resource index
        Route::get('/', 'ResourceController@index')
        ->name('admin.resources.index');


        Route::get('/{slug}',  'ResourceController@show')
            ->name('admin.resources.show');


        Route::get('/{slug}/edit',  'ResourceController@edit')
            ->name('admin.resources.edit');

         Route::delete('/{resource}/delete',  'ResourceController@destroy')
            ->name('admin.resources.delete');


        Route::get('/{resource}/unpublish',  'ResourceController@unpublish')
            ->name('admin.resources.unpublish');


        Route::patch('/{slug}/update',  'ResourceController@update')
            ->name('admin.resources.update');



        // list s3 index
        Route::post('/list-s3-files', 'ResourceController@listS3Files')
        ->name('admin.resources.lists3');

    });
});


