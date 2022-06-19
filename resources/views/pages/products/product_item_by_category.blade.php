<div class="d-flex flex-wrap justify-content-center product-list-content carousel-row" style="width: 100%; min-width: 100%; margin-left: 0px;">
    @foreach($row as $product)
        @include("pages.products.product_item_by_category_card",["product" => $product])
    @endforeach
</div>