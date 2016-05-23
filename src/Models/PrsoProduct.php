<?php

namespace Commerce\Productso\Models;

use Illuminate\Database\Eloquent\Model;
use Angrydeer\Attachfiles\AttachableTrait;
use Angrydeer\Attachfiles\AttachableInterface;
use Request;
use Sentinel;
use File;
use Carbon\Carbon;
//use Nicolaslopezj\Searchable\SearchableTrait;

class PrsoProduct extends Model implements AttachableInterface
{
    use AttachableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
/*    protected $searchable = [
        'columns' => [
            'name' => 10,
        ],
    ];*/
    public $imgPath = 'images/uploads/product/';
    protected $fillable = [
        'name', 'slug', 'price', 'old_price', 'stock', 'total_stock', 'note', 'description', 'expired', 'published_at', 'views', 'show', 'recommend'
    ];
    public function getExpiredAttribute($date){
        return Carbon::parse($date)->format('m/d/Y');
    }

    public function getDiscountAttribute() {
      return round(100-(($this->price*100)/$this->old_price),2);
    }

    public function getSaveManyAttribute() {
      return $this->old_price - $this->price;
    }

    public function getSoldAttribute() {
      return round($this->stock*100/$this->total_stock, 2);
    }

    public function expired(){
        return $this->orderBy('expired','asc')->published();
    }

    public function allExpired(){
        return $this->orderBy('expired','asc')->where('published_at','<',Carbon::now());
    }

    public function top(){
        return $this->orderBy('views','desc')->published();
    }
    public function filter(){
         return $this->orderBy('expired','asc')->published();
    }
    public function getPublished(){
         return $this->published();
    }
    public function recommendation(){
        return $this->latest()->published()->where('recommend',1);
    }
    public function last(){
       return $this->latest()->published();
    }
    public function scopePublished($query){
        return $query->where('show',1)->where('published_at','<',Carbon::now())->where('expired','>',Carbon::now());
    }

    public function scopeHasSeller($query,$getSeller=null){
        if (!empty($getSeller)) {
            return $query->whereIn('seller', $getSeller);
        }
        return null; 
    }

    public function scopeHasConditions($query,$getConditions=null){
        if (!empty($getConditions)) {
            return $query->whereIn('condition', $getConditions);
        }
        return null; 
    }
    public function scopeSearch($query,$search=null){
        if (!empty($search)) {
            $i = 0;
            foreach($search as $key => $value){
                if ($i == 0) {
                    $query->where('name', 'LIKE', "%$value%");
                }else{
                    $query->orWhere('name', 'LIKE', "%$value%");
                }
                $i++;
            }
            return $query;
        }
        return null; 
    }
    public function scopeOrderByProduct($query,$sort=null){
        if (!empty($sort)) {
            if ($sort == 'expire') {
                return $query->orderBy('expired','asc');
            }
            elseif($sort == 'asc'){
                return $query->orderBy('name','asc');
            }
            elseif($sort == 'desc'){
                return $query->orderBy('name','desc');
            }
            elseif($sort == 'top'){
                return $query->orderBy('views','desc');
            }
            elseif($sort == 'new'){
                return $query->orderBy('published_at','desc');
            }
        }
        return null; 
    }

    public function scopeHasCategories($query,$getCategory=null){
        if (!empty($getCategory)) {
            return $query->whereHas('categories', function ($query) use ($getCategory) {
                    $query->whereIn('prso_category_id', $getCategory);
                });
        }
        return null; 
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

   /* public function parentSeller()
    {
        return $this->belongsToMany('Commerce\Productso\Models\PrsoSeller', 'prso_seller_prso_product');
    }
    public function seller()
    {
        return $this->belongsToMany('Commerce\Productso\Models\PrsoSeller', 'prso_seller_prso_product');
    }
    public function setSellerAttribute($seller)
    {
        // перепрописываем отношения с таблицей категорий
        $this->seller()->detach();
        if ( ! $seller) return;
        if ( ! $this->exists) $this->save();
        $this->seller()->attach($seller);
    }

    public function getSellerAttribute($seller)
    {
        return array_pluck($this->seller()->get()->toArray(), 'id');
    }
*/

    public function parentCategories()
    {
        return $this->belongsToMany('Commerce\Productso\Models\PrsoCategory');
    }



    public function categories()
    {
        return $this->belongsToMany('Commerce\Productso\Models\PrsoCategory');
    }
    public function setCategoriesAttribute($categories)
    {
        // перепрописываем отношения с таблицей категорий
        $this->categories()->detach();
        if ( ! $categories) return;
        if ( ! $this->exists) $this->save();
        $this->categories()->attach($categories);
    }

    public function getCategoriesAttribute($categories)
    {
        return array_pluck($this->categories()->get()->toArray(), 'id');
    }



    public function getImageAttribute($value)
    {
        //add full path to image
        return '/'.$this->imgPath.$value;
    }

    public function setImageAttribute($value)
    {
        //remove file
        if (is_null($value)) {

            $image = $this->imgPath .$this->attributes['image'];
            if (File::exists($image)) {
                File::delete($image);
            }

            //clean field
            $this->attributes['image'] = null;

        } else { //add file

            //get name from path
            $imageName = last(explode('/', $value));

            //save in field only image name (without upload directory)
            $this->attributes['image'] = $imageName;

            //move image to a new directory
            if (File::exists($value)) {
                File::move($value, $this->imgPath . $imageName);
            }
        }
    }

}