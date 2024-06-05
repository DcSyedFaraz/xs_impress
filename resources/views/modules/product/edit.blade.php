@php
    use App\Http\Controllers\ProductController;

    $productClass = new ProductController();
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight clearfix">
            <div
                style="border-radius: 5px; display: inline-block; width: 85px; height: 85px; background-image: url({{ $productClass->getProductImage($product->id)->image_url }}); background-size: cover">
            </div>
            <div style="display: inline-block; margin-left: 10px">
                {{ $productClass->getProductDetailInDe($product->id)->name }}
                <br>
                <small
                    style="font-size: 16px">{{ $productClass->getProductCustomSku($product->sku)->custom_sku }}</small>
                <br>
                <small style="font-size: 12px">{{ $product->sku }}</small>
                <br>
                <smal style="color: #c03; font-size: 16px">{{ $productClass->getSupplier($product->id)->name }}</smal>
                <br>
            </div>
            <a href="{{ $_COOKIE['filter_product_page'] }}" class="btn btn-sm btn-danger float-end">Go Back to
                Products</a>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h5>Gallery</h5>
                    <p>
                        @foreach (\App\Models\ProductImage::where('product_detail_id', $productClass->getProductDetailInDe($product->id)->id)->get() as $pImage)
                            <div
                                style="border-radius: 5px; display: inline-block; width: 150px; height: 150px; background-image: url({{ $pImage->url }}); background-size: cover">
                            </div>
                        @endforeach
                    </p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-5">
                <div class="p-6 text-gray-900">
                    <h5>Description</h5>
                    <p>
                        {{ $product->details->description }}
                        {{-- {{ $productClass->getProductDetailInDe($product->id)->description }} --}}
                    </p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-5">
                <div class="p-6 text-gray-900">
                    <h5>Category</h5>
                    <p>
                        @if ($product && $product->static_content)
                            @if ($product->static_content instanceof \Illuminate\Support\Collection)
                                {{ $product->static_content->get('category', '') }}
                            @else
                                {{ $product->static_content->category ?? '' }}
                            @endif
                        @else
                            <p>Static content not available.</p>
                        @endif


                        {{-- {{ $productClass->getProductStatisCategory($product->sku) }} --}}
                    </p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-5">
                <div class="p-6 text-gray-900">
                    <h5>Non Language Dependent Details</h5>
                    <p>
                        @php
                            $details = json_decode($product->non_language_depended_product_details, true);
                        @endphp
                        @if (is_array($details))
                            @foreach ($details as $key => $detail)
                                @if (!is_array($detail))
                                    {{ $key }}: <b>{{ $detail }}</b>
                                    <br>
                                @endif
                            @endforeach
                        @endif
                        @if (!empty($product->details->config))
                            <h5>Colors:</h5>
                            @forelse ($product->details->config as $key => $config)
                                {{ $key + 1 }}: <b>{{ $config->value ?? 'N/A' }}</b> <br>
                            @empty
                                <p>No colors available.</p>
                            @endforelse
                        @endif
                        @php
                            $unstructuredInfo = json_decode($product->unstructured_information ?? '', true);
                            $releaseDate = $unstructuredInfo['ReleaseDate'] ?? 'N/A';
                        @endphp

                    <h5 class="mt-3">Last Updated</h5>
                    <b>{{ $releaseDate }}</b>

                    </p>
                </div>
            </div>

            @php
                $variants = $productClass->getProductVariants($product->id);
                // $variants = $product->details->images;
            @endphp
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-5">
                <div class="p-6 text-gray-900">
                    <h5>Variants</h5>
                    @if (!count($variants))
                        <h6>
                            <div class="">There are no variants for this product</div>
                        </h6>
                    @endif
                    <div class="" style="@if (!count($variants)) display: none; @endif">
                        <table class="table" style="text-align: left; width: 100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>Sku</th>
                                    <th>Categories</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody aria-relevant="all" aria-live="polite">
                                @foreach ($variants as $key => $variant)
                                    <tr>
                                        @php
                                            $serial =
                                                isset($_GET['page']) && $_GET['page'] != 1
                                                    ? $_GET['page'] * 10 + ($key + 1)
                                                    : $key + 1;
                                        @endphp
                                        <td>{{ $serial }}</td>
                                        <td style="width: 125px">
                                            <div
                                                style="width: 85px; height: 85px; background-image: url({{ $variant->image_url }}); background-size: cover">
                                            </div>
                                        </td>
                                        <td>
                                            {{-- @dd($variant) --}}
                                            {{ $productClass->getProductCustomSku($variant->sku)->custom_sku }}
                                            <div>
                                                <small><b>Supplier SKU:</b> {{ $variant->supplier_sku }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $allStaticContent = $productClass->getProductStatisCategory(
                                                    $variant->sku,
                                                );
                                            @endphp
                                            {{ $allStaticContent }}
                                        </td>
                                        <td>
                                            <a href="{{ URL::to('/product/edit/' . $variant->id) }}">
                                                <svg height="1.5em" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                        stroke-linejoin="round"></g>
                                                    <g id="SVGRepo_iconCarrier">
                                                        <g id="Edit / Edit_Pencil_Line_02">
                                                            <path id="Vector"
                                                                d="M4 20.0001H20M4 20.0001V16.0001L14.8686 5.13146L14.8704 5.12976C15.2652 4.73488 15.463 4.53709 15.691 4.46301C15.8919 4.39775 16.1082 4.39775 16.3091 4.46301C16.5369 4.53704 16.7345 4.7346 17.1288 5.12892L18.8686 6.86872C19.2646 7.26474 19.4627 7.46284 19.5369 7.69117C19.6022 7.89201 19.6021 8.10835 19.5369 8.3092C19.4628 8.53736 19.265 8.73516 18.8695 9.13061L18.8686 9.13146L8 20.0001L4 20.0001Z"
                                                                stroke="#B9853E" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                        </g>
                                                    </g>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                        </table>
                        <div class="d-flex mt-5">
                            <div class="mx-auto"> {{ $variants->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($product->parent_id)
                @php
                    $buyingPrice = \App\Models\ProductPriceCountryBased::where([
                        ['product_id', $product->id],
                        ['type', 'General Buying Price'],
                    ])->get();
                    $sellingPrice = \App\Models\ProductPriceCountryBased::where([
                        ['product_id', $product->id],
                        ['type', 'Recommended Selling Price'],
                    ])->get();
                @endphp
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-5">
                    <div class="p-6 text-gray-900">
                        @if ($buyingPrice)
                            <h5>Buying Price: </h5>
                            <table style="width: 100%">
                                <thead>
                                    <tr>
                                        @foreach ($buyingPrice as $productBuyingPrice)
                                            <th>{{ $productBuyingPrice->quantity }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach ($buyingPrice as $productBuyingPrice)
                                            <td>{{ $productBuyingPrice->price }}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <br>
                        @endif
                        @if ($sellingPrice)
                            <h5>Selling Price: </h5>
                            <table style="width: 100%">
                                <thead>
                                    <tr>
                                        @foreach ($sellingPrice as $productSellingPrice)
                                            <th>{{ $productSellingPrice->quantity }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach ($sellingPrice as $productSellingPrice)
                                            <td>{{ $productSellingPrice->price }}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            @endif

            @php
                $printingOptions = $productClass->printingPositions($product->id);
            @endphp
            @if (count($printingOptions))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-5">
                    <div class="p-6 text-gray-900">
                        <h5>Printing Options</h5>
                        <div>
                            <ul class="list-unstyled">
                                @foreach ($printingOptions as $key => $printingOption)
                                    <li class="mb-4">
                                        @php
                                            $positionTextDe = $productClass->printingPositionsTextDe(
                                                $printingOption->id,
                                            );
                                            $positionOptions = $productClass->printingPositionOptions(
                                                $printingOption->id,
                                            );
                                            $positionImages = json_decode($positionTextDe->images);
                                        @endphp
                                        <div>
                                            <div class="mb-2">{{ $key + 1 . '. ' . $positionTextDe->name }}</div>
                                            <div class="mb-2">
                                                <small><b>Images</b></small>
                                                @if (count((array) $positionImages))
                                                    @foreach ($positionImages as $positionImage)
                                                        <img src="{{ $positionImage->Url }}" style="width: 100px">
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div>
                                                <small><b>Options</b></small>
                                                <ul class="list-unstyled">
                                                    @foreach ($positionOptions as $positionOption)
                                                        @php
                                                            $optionSellingPrice = $productClass->printingSellingPrice(
                                                                $positionOption->id,
                                                            );
                                                            $options = json_decode(
                                                                $positionOption->imprint_texts,
                                                                true,
                                                            );
                                                        @endphp
                                                        @if (isset($options['de']))
                                                            <li>{{ $options['de']['Name'] }}</li>
                                                        @endif
                                                        <div class="mt-2">
                                                            <small><b>Selling Price</b></small>
                                                            <table style="width: 100%">
                                                                <thead>
                                                                    <tr>
                                                                        @foreach ($optionSellingPrice as $sellPrice)
                                                                            <th>{{ $sellPrice->quantity }}</th>
                                                                        @endforeach
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        @foreach ($optionSellingPrice as $sellPrice)
                                                                            <td>{{ $sellPrice->price }}</td>
                                                                        @endforeach
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <hr>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if (!$product->parent_id)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-5">
                    <div class="p-6 text-gray-900">
                        <h5>Update Category</h5>
                        <form action="{{ URL::to('product/update/' . $product->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <div class="clearfix">
                                    <label>Select Category(ies)</label>
                                    <select class="form-control js-example-basic-single" name="category[]"
                                        style="height: 300px; margin-top: 10px" multiple>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->key }}"
                                                @if (in_array($category->de, explode(' | ', (string) $productClass->getProductStatisCategory($product->sku)))) selected @endif>{{ $category->de }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-secondary mt-4">Save</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>
</x-app-layout>
