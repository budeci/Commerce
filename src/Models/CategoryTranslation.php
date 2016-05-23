<?php
namespace Commerce\Productso\Models;
use Illuminate\Database\Eloquent\Model;
use Request;
use Sentinel;

class CategoryTranslation extends Model {
    protected $table      = 'category_translation';
    public $timestamps    = false;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'meta_keyword',
        'prso_category_id',
        'locale',

    ];

/*    protected $hidden = [
        'prso_category_id',
        'language_id',
    ];*/
    public function categories()
    {
        return $this->belongsToMany('Commerce\Productso\Models\PrsoCategory');
    }
    public function setSlugAttribute($slug)
    {
        if($slug=='') $slug = str_slug(Request::get('name'));
        if($cat= self::where('slug',$slug)->first()){
            $idmax=self::max('id')+1;
            if(isset($this->attributes['id']))
            {
              if ($this->attributes['id'] != $cat->id ){
               $slug=$slug.'_'.++$idmax;
              }
            }
            else
           {
              if (self::where('slug',$slug)->count() > 0)
              $slug=$slug.'_'.++$idmax;
           }
         }
      $this->attributes['slug']=$slug;
    }

}


