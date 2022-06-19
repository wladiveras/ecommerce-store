<div class="d-flex flex-wrap product-list-content teste">
    @foreach($row as $product)
        @include("pages.products.product_item_suggestion_card",["product" => $product])
    @endforeach
</div>