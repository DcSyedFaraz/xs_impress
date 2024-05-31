@php
    use \App\Http\Controllers\ProductController;

    $productClass = new ProductController;

    $filterXsSku = request()->has('xs_sku')? request()->input('xs_sku') : '';
    $filterSku = request()->has('sku')? request()->input('sku') : '';
    $filterProductName = request()->has('product_name')? request()->input('product_name') : '';
    $filterProductDesc = request()->has('product_desc')? request()->input('product_desc') : '';
    $filterSupplier = request()->has('supplier') && request()->input('supplier') != 'Select'? request()->input('supplier') : '';
    $filterCategory = request()->has('category') && request()->input('category') != 'Select'? request()->input('category') : '';
@endphp
<x-app-layout>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Products') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="">
                    <div class="filters">
                        <h5>Filters</h5>
                        <form action="" method="GET">
                            <div class="row">
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label>XS Sku</label>
                                        <input type="text" class="form-control" name="xs_sku" value="{{ $filterXsSku }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label>Sku</label>
                                        <input type="text" class="form-control" name="sku" value="{{ $filterSku }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label>Product Name</label>
                                        <input type="text" class="form-control" name="product_name" value="{{ $filterProductName }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label>Product Text</label>
                                        <input type="text" class="form-control" name="product_desc" value="{{ $filterProductDesc }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label>Supplier</label>
                                        <select class="form-control" name="supplier">
                                            <option>Select</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" @if($filterSupplier == $supplier->id) selected @endif>{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select class="form-control" name="category">
                                            <option class="">Select</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->key }}" @if($filterCategory == $category->key) selected @endif>{{ $category->de }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm mt-3">Apply Filter</button>
                            @if($filterSku || $filterXsSku || $filterCategory || $filterSupplier || $filterProductName || $filterProductDesc)
                                <a href="{{ URL::to('product') }}" class="btn btn-light btn-sm mt-3">Clear Filter</a>
                            @endif
                        </form>
                    </div>
                   <div class="clearfix mt-4">
                       <div class="float-start">
                           Total no of products: <b>{{ $productClass->getCollectionSize($filterXsSku, $filterSku, $filterSupplier, $filterCategory, $filterProductName, $filterProductDesc) }}</b>
                       </div>
                       <div  class="float-end">
                           <form action="{{ URL::to('product/delete') }}" method="POST">
                               @csrf
                               <input type="hidden" id="selected-product-ids" name="product_ids" value="">
                               <button href="{{ URL::to('product') }}" class="btn btn-danger btn-sm btn-delete-products" disabled>Delete Products</button>
                           </form>
                       </div>
                       <div  class="float-end pt-1 mx-3">
                           <input type="checkbox" class="show-hide-variant" name="1" <?php if(isset($_COOKIE['product_grid_view_mode'])): ?> checked <?php endif; ?>>
                           Hide Variants
                       </div>
                   </div>
                    @if(count($products))
                        <table class="table" style="text-align: left; width: 100%">
                            <thead>
                            <tr>
    {{--                            <th></th>--}}
                                <th>
                                    <input type="checkbox" class="parent-checkbox" name="product_ids[]">
                                </th>
                                <th>XS Sku</th>
                                <th></th>
                                <th>Supplier</th>
                                <th>Sku</th>
                                <th>Type</th>
                                <th>Variants</th>
                                <th>Categories</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody aria-relevant="all" aria-live="polite">
                            @foreach($products as $key => $product)
                                <tr>
                                    @php
                                        $serial = isset($_GET['page']) && $_GET['page'] != 1? ($_GET['page']*10) + ($key+1) : ($key+1);
                                    @endphp
    {{--                                <td>{{ $serial }}</td>--}}
                                    <td>
                                        @if(!$product->parent_id)
                                        <input type="checkbox" class="child-checkbox" name="product_ids[]" value="{{ $product->id }}">
                                        @endif
                                    </td>
                                    <td>{{ $productClass->getProductCustomSku($product->sku)->custom_sku }}</td>
                                    <td style="width: 125px">
                                        <div style="width: 85px; height: 85px; background-image: url({{ $product->image_url }}); background-size: cover"></div>
                                    </td>
                                    <td>
                                        {{ $productClass->getSupplier($product->id)->name }}
                                    </td>
                                    <td>
                                        {{ $product->sku }}
                                        <div>
                                            <small><b>Supplier SKU:</b> {{ $product->supplier_sku }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $product->parent_id? 'Variant' : 'Parent' }}</td>
                                    <td>{{ $product->parent_id? 0 : $productClass->getVariantCount($product->id) }}</td>
                                    <td>
                                        {{ $productClass->getProductStatisCategory($product->sku) }}
                                    </td>
                                    <td class="table-action" style="text-align: end">
                                        @if(!$product->parent_id)
                                        <a href="{{ URL::to('/product/edit/'.$product->id) }}">
                                            <svg height="1.5em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="Edit / Edit_Pencil_Line_02"> <path id="Vector" d="M4 20.0001H20M4 20.0001V16.0001L14.8686 5.13146L14.8704 5.12976C15.2652 4.73488 15.463 4.53709 15.691 4.46301C15.8919 4.39775 16.1082 4.39775 16.3091 4.46301C16.5369 4.53704 16.7345 4.7346 17.1288 5.12892L18.8686 6.86872C19.2646 7.26474 19.4627 7.46284 19.5369 7.69117C19.6022 7.89201 19.6021 8.10835 19.5369 8.3092C19.4628 8.53736 19.265 8.73516 18.8695 9.13061L18.8686 9.13146L8 20.0001L4 20.0001Z" stroke="#B9853E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g> </g></svg>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        <div class="d-flex mt-5">
                            <div class="mx-auto"> {{ $products->appends(request()->except('page'))->links() }}</div>
                        </div>
                    @else
                        <h6>
                            <div class="pt-4">No records found</div>
                        </h6>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('.show-hide-variant').on('click', function(){
            if($(this).is(':checked')){
                document.cookie = "product_grid_view_mode=without_variant; expires=Thu, 18 Dec 2050 12:00:00 UTC; path=/";
                window.location.reload();
            }else{
                document.cookie = "product_grid_view_mode=without_variant; expires=Thu, 18 Dec 2005 12:00:00 UTC; path=/";
                window.location.reload();
            }
        });
    });

    var productIds = [];

    $('.parent-checkbox').on('click', function(){
        if(!$(this).is(':checked')){
            $('.child-checkbox').prop("checked", false);
            $('.btn-delete-products').prop('disabled', true);
            productIds = [];
            $('#selected-product-ids').val(productIds.join());
            return;
        }

        if($('.child-checkbox:checked').length < 20){
            $('.child-checkbox').prop("checked", false);
        }

        $.each($('.child-checkbox'), function(i, v){
            $('.btn-delete-products').prop('disabled', false);
            $(v).prop("checked", true);

            productIds.push($(v).val());
        });

        $('#selected-product-ids').val(productIds.join());
    });

    $('.child-checkbox').on('click', function() {
        if ($('.child-checkbox:checked').length) {
            $('.btn-delete-products').prop('disabled', false);
        } else {
            $('.btn-delete-products').prop('disabled', true);
        }

        var index = $.inArray($(this).val(), productIds);

        if(index !== -1){
            productIds.splice(index, 1);
        }else{
            productIds.push($(this).val());
        }

        $('#selected-product-ids').val(productIds.join());
    });
</script>
</x-app-layout>

