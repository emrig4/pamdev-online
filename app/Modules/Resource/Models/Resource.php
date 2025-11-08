<?php
namespace App\Modules\Resource\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Modules\Resource\Helpers\SubFieldHelper;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Modules\File\Http\Traits\HasFile;
use Illuminate\Support\Facades\Storage;
// use Silber\Bouncer\BouncerFacade as Bouncer;
use App\Modules\Resource\Models\ResourceAuthor;
use App\Modules\Resource\Models\ResourceReview;
use Illuminate\Database\Eloquent\Builder;

class Resource extends Model
{
    use Sluggable, HasFile;
    
    protected static function boot() {
	    parent::boot();
        static::addGlobalScope(function (Builder $builder) {
           return $builder->where('is_published', true)->orderBy('created_at', 'ASC');
        });
	    static::creating(function ($resource) {
            $user = auth()->user();
	        $resource->sub_fields = SubFieldHelper::processSubfields($resource->sub_fields);
            $resource->user_id = $user->id;
            // self::createCover($resource->title)
            // Bouncer::allow($user)->toOwn(self::class);
	    });
	}
    
    /**
     * Get the cover image URL for this resource
     * Returns default image if no cover is set
     */
    public function getCoverImageAttribute()
    {
        // If a cover image is uploaded, return its URL
        if (isset($this->attributes['cover']) && $this->attributes['cover']) {
            // Check if it's a full URL (uploaded to cloud)
            if (filter_var($this->attributes['cover'], FILTER_VALIDATE_URL)) {
                return $this->attributes['cover'];
            }
            // Check if it's stored locally
            if (Storage::disk('public')->exists($this->attributes['cover'])) {
                return Storage::url($this->attributes['cover']);
            }
        }
        
        // Return default project cover image
        return asset('images/default-project-cover.png');
    }
    
    public function createCover($title)  
    {  
       $img = Image::make(public_path('images/codermen.jpg'));  
       $img->text($title, 120, 100, function($font) {  
          $font->file(public_path('path/font.ttf'));  
          $font->size(28);  
          $font->color('#4285F4');  
          $font->align('center');  
          $font->valign('bottom');  
          $font->angle(0);  
        });  
       $img->save(public_path('images/text_with_image.jpg'));  
    }
    
    /**
     * The attributes that should be guarded for arrays.
     *
     * @var array
     */
    protected $guarded = [
        'id'
    ];
    
    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
    
    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }
    
    public function related() {
        return self::where('field', $this->field)->limit(12)->get();
    }
    
    public function reviews() {
        return $this->hasMany(ResourceReview::class);
    }
    
    public function reports() {
        return $this->hasMany(ResourceReport::class);
    }
    
    public function rating() {
        $reviews = $this->reviews()->get();
        $rating = 0;
        foreach ($reviews as $review) {
            $rating += $review->rating;
        }
        $rate = 0;
        if(count($reviews) > 0){
            $rate = $rating/count($reviews);
        }
        return $rate;
    }
    
    public function authors(){
        return $this->hasMany(ResourceAuthor::class);
    }
    
    public function author(){
        return $this->authors()->whereIsLead(1)->first();
    }
    
    public function isNew() {
       return $this->created_at > now()->subDays(7);
    }
    
    public function isTop() {
        return false;
    }
}