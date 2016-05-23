<?php

namespace Commerce\Productso\Http\Controllers;
use App\Http\Controllers\Controller;
use Commerce\Productso\Models\PrsoShop as Shop;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PrsoShopController extends Controller
{
  public function show($slug='root')
    {
        // Если запрос пришел не на конкретную категорию, а на раздел категорий, отдаем коллекцию категорий верхнего уровня
        if ($slug == 'root')
        {
            $nodes= Shop::whereIsRoot()->get();
            $many = true;
            return view('Productso::category_show', compact('nodes','many'));
        }
        // Иначе отдаем запрашиваемую категорию c товарами
        if ( $node = Shop::where('slug',$slug)->first()) {

            $products=Shop::find($node->id)->products()->paginate(Shop::$productPerPage);
            $many = false;
            return view('Productso::category_show', compact('node','many','products'));
        }
        // ну или посылаем на 404 если нет такой
        abort(404);
    }
}

