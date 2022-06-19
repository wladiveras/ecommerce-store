<div class="d-flex flex-wrap justify-content-center product-list-content teste">
    @foreach($row as $product)
        @include("pages.products.product_item_card",["product" => $product])
    @endforeach
</div>