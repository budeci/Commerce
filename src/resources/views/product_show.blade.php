<h1>Пример breadcrumbs</h1>
@if($pathCategory)
    <style>.ibl {display:inline-block;}</style>
    <li class="ibl"><a href="{{URL::to('/')}}">Главная</a></li>
    @foreach($pathCategory->getAncestors() as $descend)
        <li class="ibl">-><a href="{{URL::to('/category/'.$descend->slug)}}">{{$descend->name}}</a></li>
    @endforeach
    <li class="ibl">-><a href="{{URL::to('/category/'.$pathCategory->slug)}}">{{$pathCategory->name}}</a></li>
    <li class="ibl">->{{$product->name}}</li>
@endif

<h1>Вывод изображений товара</h1>
@if($product->attaches)
    @foreach($product->attaches as $attach)
        <img src="{{URL::to($attach->filename)}}" alt="{{$attach->alt}}" title="{{$attach->title}}">
    @endforeach
@endif

<h3>Товар: {{$product->name}}</h3>

<h3>Цена: {{$product->cost}}</h3>
<h3>Артикул: {{$product->artikul}}</h3>

<h3>Аннотация:</h3>
{!! $product->note !!}

<h3>Описание:</h3>
{!!$product->note !!}

<h3>Входит в категории:</h3>

{{info($product->categories)}}

@foreach($product->parentCategories as $cat)

    <li>-><a href="{{URL::to('/category/'.$cat->slug)}}">{{$cat->name}}</a></li>
@endforeach