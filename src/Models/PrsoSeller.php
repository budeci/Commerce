<?php

namespace Commerce\Productso\Models;
use Illuminate\Database\Eloquent\Model;
use Angrydeer\Attachfiles\AttachableTrait;
use Angrydeer\Attachfiles\AttachableInterface;
use Request;
use Sentinel;
use File;
use Carbon\Carbon;

class PrsoSeller extends Model implements AttachableInterface
{
    use AttachableTrait;

    public $imgPath  = 'images/uploads/seller/';
    protected $table = 'prso_seller';
    protected $fillable = [
        'name', 'slug', 'description', 'views', 'image', 'show', 'published_at'
    ];

    public static $productPerPage = 30;

    public function getImageAttribute($value)
    {
        //add full path to image
        return '/'.$this->imgPath.$value;
    }
    public function setPublishedAtAttribute($value){
        if (is_null($value)) {
            $this->attributes['published_at'] = Carbon::now(); 
        }
    }

    public function setImageAttribute($value)
    {
        //remove file
        if (is_null($value) or $value == "") {

            $image = $this->imgPath.$this->attributes['image'];
            
            if (File::exists($image)) {
                File::delete($image);
            }

            //clean field
            $this->attributes['image'] = null;

        } else { //add file

            //get name from path
            if (Request::hasFile('image')) {
                $extension = Request::file('image')->getClientOriginalExtension();
            }else{
                $extension = File::extension($value);
            }
            
            
            $fileName = md5(time()).'.'.$extension; // renameing image
            //save in field only image name (without upload directory)
            if (isset($this->attributes['image']) && !empty($this->attributes['image']) && File::exists($this->imgPath . $this->attributes['image'])) {
                $fileName = $this->attributes['image'];
            }
            
            $this->attributes['image'] = $fileName;

            //move image to a new directory
            if (File::exists($value)) {
                File::move($value, $this->imgPath . $fileName);
               
            }
        }
    }

    public function topShop(){
        return $this->orderBy('views','desc')->published();
    }

    public function allShop(){
        return $this->published();
    }

    public function last(){
       return $this->latest()->published();
    }

    public function scopePublished($query){
        return $query->where('show',1)->where('published_at','<=',Carbon::now());
    }

    public function products()
    {
        return $this->belongsToMany('Commerce\Productso\Models\PrsoProduct');
    }

    public function setSlugAttribute($slug)
    {

      if($slug=='') $slug = str_slug(Request::get('name'), "-");
      else $slug = str_slug($slug, "-");
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

    public function getPhotosAttribute($value)
    {
        return array_pluck($this->attaches()->get()->toArray(), 'filename');
    }

    public function setPhotosAttribute($images)
    {
        $imgtitles = Request::get('imgtitle');
        $imgalts   = Request::get('imgalt');
        $imgdescs  = Request::get('imgdesc');
        $this->save();
        $i=0;
        foreach($images as $image)
        {
            $this->updateOrNewAttach($image, $imgtitles[$i], $imgalts[$i], $imgdescs[$i]);
            $i++;
        }

/*
* Очистка мусора за собой.  Функция updateOrNewAttach за собой приберает, но  могут оставаться картинки, которые не были поданы в сохранение 
*(редактировали категорию, перебрали кучу картинок, в конце концов отменили 
* сохранениe)
* Для этого и нужен id админа, чтобы чистил за собой а не общую папку аплоадс
* может в этот момент еще кто-то что-то правит.
*/

        $path = config('admin.imagesUploadDirectory').'/'.Sentinel::check()->id;
        $files = glob(public_path($path)."/*");
        if (count($files) > 0) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }


        $this->keepOnly($images);
    }

}