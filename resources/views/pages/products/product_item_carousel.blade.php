<div class="d-flex flex-wrap justify-content-center product-list-content carousel-row" style="width: 100%; min-width: 100%;">
    @foreach($row as $product)
        @include("pages.products.product_item_card",["product" => $product])
    @endforeach
</div>