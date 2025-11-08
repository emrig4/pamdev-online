<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use App\Modules\File\Models\File;
use App\Modules\Resource\Models\Resource;
use App\Modules\Resource\Models\ResourceAuthor;
use App\Models\User;
use Illuminate\Support\Facades\Storage;



class ResourcePublishImport implements ToModel, WithStartRow, WithEvents
{
    public $sheetName;


    
    public function __construct(){
        $this->sheetName = '';
    }


    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    *    
    */
    public function model(array $row)
    {
      try {
        
        $filename = $row[0];
        $user_id = auth()->user()->id;
        $fileMeta = Storage::disk('s3')->getMetaData($filename);
        if(!$file = File::where('filename', $fileMeta['filename'])->first() ){
            $file = File::create([
                'user_id' => $user_id,
                'disk' => 's3',
                'filename' => $fileMeta['filename'],
                'path' => $fileMeta['path'],
                'extension' => $fileMeta['extension'],
                'mime' => $fileMeta['mimetype'],
                'size' =>$fileMeta['size'],
                'location' => 'upload',
                // 'page_count' => self::countPages($path),
            ]);

            $resource =  Resource::create(
              [
                'title' => $row[1],
                'overview' => $row[2],
                'publication_year' => $row[3],
                'coauthors' => $row[5], //comma delimited strings
                'type' => $row[6], // resource field slug
                'field' => $row[7], //  resource field slug
                'sub_fields' => $row[8], //comma delimited strings
                'currency' => $row[9],
                'price' => $row[10],
                'preview_limit' => $row[11],
                'isbn' => $row[12],
                'is_featured' => $row[13],
                'is_private' => $row[14],
                'is_active' => $row[15],
              ]
            );

            if($file && $resource){
                $entity = \DB::table('entity_files')->insert([
                    'file_id' => $file->id,
                    'entity_type'=>'App\Modules\Resource\Models\Resource',
                    'entity_id'=> $resource->id,
                    'label'=>'main_file',
                    'created_at'=>$resource->created_at,
                    'updated_at'=>$resource->updated_at,
                ]);


                ResourceAuthor::create([
                    'fullname' =>  $row[4],
                    'resource_id' => $resource->id,
                    'is_lead' => 1,
                    'username' => \Str::slug($row[4])
                ]);

                // save coauthors
                $authors = explode(',',  $row[5]);
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
            }
        }
        
        return;

      } catch (\Throwable $th) {
        throw $th;
      }
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $this->sheetName = $event->getSheet()->getDelegate()->getTitle();
            }
        ];
    }
}
