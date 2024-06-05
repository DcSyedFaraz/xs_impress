<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CustomProductSku;
use App\Models\DeletedProduct;
use App\Models\Product;
use App\Models\ProductImprintPosition;
use App\Models\ProductStaticContent;
use App\Models\ProductImprintPositionLocationText;
use App\Models\ProductImprintPositionOption;
use App\Models\ProductImprintPositionOptionPriceCountryBased;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Models\ProductPriceCountryBased;
use Illuminate\Http\Request;
use Auth;

class ProductController extends Controller
{
    public function products(Request $request){
        setcookie('filter_product_page', url()->to($_SERVER['REQUEST_URI']), time() + (86400 * 30), "/");

        $products = Product::whereDoesntHave('deletedProducts')->where('product_details.language', 'de');

        if(isset($_COOKIE['product_grid_view_mode'])){
            $products = $products->where('type', 'parent');
        }

        if($request->has('xs_sku') && $request->input('xs_sku') != ''){
            $products = $products->where('custom_product_skus.custom_sku', 'RLIKE', $request->input('xs_sku'));
            $products->join('custom_product_skus', 'custom_product_skus.orignal_sku', 'products.sku');
        }

        if($request->has('sku') && $request->input('sku') != ''){
            $products = $products->where('products.sku', 'RLIKE', $request->input('sku'));
        }

        if($request->has('product_name') && $request->input('product_name') != ''){
            $products = $products->where('product_details.name', 'RLIKE', $request->input('product_name'));
        }

        if($request->has('product_desc') && $request->input('product_desc') != ''){
            $products = $products->where('product_details.description', 'RLIKE', $request->input('product_desc'));
        }

        if($request->has('supplier') && $request->input('supplier') != 'Select'){
            $products = $products->where('supplier_products.supplier_id', $request->input('supplier'));
            $products->join('supplier_products', 'supplier_products.product_id', 'products.id');
        }

        if($request->has('category') && $request->input('category') != 'Select'){
            $products = $products->where('product_static_contents.category', 'RLIKE', $request->input('category'));
            $products->join('product_static_contents', 'product_static_contents.sku', 'products.sku');
        }

        $products = $products->join('product_details', 'products.id', 'product_details.product_id')
            ->join('product_images', 'product_details.id', 'product_images.product_detail_id')
            ->select('products.*', 'product_images.url as image_url')
            ->orderBy('id', 'DESC')
            ->paginate(20);

        $categories = Category::get();
        $suppliers = Supplier::get();

        return view('modules.product.get', compact(['products', 'categories', 'suppliers']));
    }

    public function editProduct($id){
        $product = Product::with('details.config')->findOrFail($id);
        $categories = Category::get();
        // dd($product);
        return view('modules.product.edit', compact(['product', 'categories']));
    }

    public function getProductImage($id){
        return Product::where([['products.id', $id], ['product_details.language', 'de']])
            ->join('product_details', 'products.id', 'product_details.product_id')
            ->join('product_images', 'product_details.id', 'product_images.product_detail_id')
            ->select('product_images.url as image_url')
            ->first();
    }

    public function getProductDetailInDe($id){
        return Product::where([['products.id', $id], ['product_details.language', 'de']])
            ->join('product_details', 'products.id', 'product_details.product_id')
            ->select('product_details.*')
            ->first();
    }

    public function getProductVariants($id){
        return Product::where([['products.parent_id', $id], ['product_details.language', 'de']])
            ->join('product_details', 'products.id', 'product_details.product_id')
            ->join('product_images', 'product_details.id', 'product_images.product_detail_id')
            ->select('products.*', 'product_images.url as image_url')
            ->paginate(20);;
    }

    public function updateProduct(Request $request, $id){
        $product = Product::find($id);
        $productStaticContent = ProductStaticContent::where('sku', $product->sku)->get();

        foreach($productStaticContent as $staticContent) {
            ProductStaticContent::where('id', $staticContent->id)->update([
                'category' => implode(',', $request->category)
            ]);
        }

        $childProduts = Product::where('parent_id', $product->id)->get();

        foreach($childProduts as $childProdut){
            $productStaticContent = ProductStaticContent::where('sku', $childProdut->sku)->get();

            foreach($productStaticContent as $staticContent) {
                ProductStaticContent::where('id', $staticContent->id)->update([
                    'category' => implode(',', $request->category)
                ]);
            }
        }

        return redirect()->back()->with('message', 'Product category has been updated.');
    }

    public function getProductStatisCategory($productSku){
        $staticContent = ProductStaticContent::where([['sku', $productSku], ['language', 'de']])->first();

        $categories = [];

        foreach(explode(',', (string)$staticContent->category) as $eachCategory){
            $category = Category::where('key', $eachCategory)->first();

            if($category){
                $categories[] = $category->de;
            }
        }


        return implode(' | ', $categories);
    }

    public function getProductCustomSku($orignalSku){
        return CustomProductSku::where('orignal_sku', $orignalSku)->first();
    }
    public function getCategoryAndDescription($productSku){
        return ProductStaticContent::where('sku', $productSku)->get();
    }

    public function getSupplier($productId){
        return SupplierProduct::where('product_id', $productId)
            ->join('suppliers', 'suppliers.id', 'supplier_products.supplier_id')
            ->select('suppliers.*')
            ->first();
    }

    public function getVariantCount($productId){
        return Product::where('parent_id', $productId)->count();
    }

    public function getCollectionSize($xsSku, $sku, $supplier, $category, $productName, $productDesc){
        $products = Product::whereDoesntHave('deletedProducts')->where('products.id', '>', 0);

        if(isset($_COOKIE['product_grid_view_mode'])){
            $products = $products->where('type', 'parent');
        }

        if($xsSku){
            $products = $products->where('custom_product_skus.custom_sku', 'RLIKE', $xsSku);
            $products->join('custom_product_skus', 'custom_product_skus.orignal_sku', 'products.sku');
        }

        if($sku){
            $products = $products->where('products.sku', 'RLIKE', $sku);
        }

        if($productName){
            $products = $products->where('pd1.name', 'RLIKE', $productName);
            $products->join('product_details as pd1', 'pd1.product_id', 'products.id');
        }

        if($productDesc){
            $products = $products->where('pd2.description', 'RLIKE', $productDesc);
            $products->join('product_details as pd2', 'pd2.product_id', 'products.id');
        }

        if($supplier){
            $products = $products->where('supplier_products.supplier_id', $supplier);
            $products->join('supplier_products', 'supplier_products.product_id', 'products.id');
        }

        if($category){
            $products = $products->where('product_static_contents.category', 'RLIKE', $category);
            $products->join('product_static_contents', 'product_static_contents.sku', 'products.sku');
        }

        return $products->count();
    }

    public function hideVariantInGrid(){
        setcookie('product_grid_view_mode', 'without_variant', time() + (86400 * 30), "/");
    }

    public function softDeleteProduct(Request $request){
        if($request->has('product_ids')){
            $productIds = explode(',', $request->input('product_ids'));

            foreach($productIds as $productId){
                $parentProduct = Product::where('id', $productId)->select('sku')->first();

                DeletedProduct::create([
                    'sku' => $parentProduct->sku,
                    'deleted_by' => Auth::user()->id,
                    'deleted_at' => now(),
                    'recovered_at' => '',
                ]);

                $childProducts = Product::where('parent_id', $productId)->select('sku')->get();

                foreach($childProducts as $childProduct){
                    DeletedProduct::create([
                        'sku' => $childProduct->sku,
                        'deleted_by' => Auth::user()->id,
                        'deleted_at' => now(),
                        'recovered_at' => '',
                    ]);
                }
            }
        }

        return redirect()->back()->with('message', 'Product has been deleted.');
    }

    public function deleteProducts(Request $request){
        $products = Product::whereHas('deletedProducts')->where('product_details.language', 'de');

        if(isset($_COOKIE['product_grid_view_mode'])){
            $products = $products->where('type', 'parent');
        }

        if($request->has('xs_sku') && $request->input('xs_sku') != ''){
            $products = $products->where('custom_product_skus.custom_sku', 'RLIKE', $request->input('xs_sku'));
            $products->join('custom_product_skus', 'custom_product_skus.orignal_sku', 'products.sku');
        }

        if($request->has('sku') && $request->input('sku') != ''){
            $products = $products->where('products.sku', 'RLIKE', $request->input('sku'));
        }

        if($request->has('supplier') && $request->input('supplier') != 'Select'){
            $products = $products->where('supplier_products.supplier_id', $request->input('supplier'));
            $products->join('supplier_products', 'supplier_products.product_id', 'products.id');
        }

        if($request->has('category') && $request->input('category') != 'Select'){
            $products = $products->where('product_static_contents.category', 'RLIKE', $request->input('category'));
            $products->join('product_static_contents', 'product_static_contents.sku', 'products.sku');
        }

        $products = $products->join('product_details', 'products.id', 'product_details.product_id')
            ->join('product_images', 'product_details.id', 'product_images.product_detail_id')
            ->select('products.*', 'product_images.url as image_url')
            ->orderBy('id', 'DESC')
            ->paginate(20);

        $categories = Category::get();
        $suppliers = Supplier::get();

        return view('modules.deleted-product.get', compact(['products', 'categories', 'suppliers']));
    }

    public function getDeletedCollectionSize($xsSku, $sku, $supplier, $category){
        $products = Product::whereHas('deletedProducts')->where('products.id', '>', 0);

        if(isset($_COOKIE['product_grid_view_mode'])){
            $products = $products->where('type', 'parent');
        }

        if($xsSku){
            $products = $products->where('custom_product_skus.custom_sku', 'RLIKE', $xsSku);
            $products->join('custom_product_skus', 'custom_product_skus.orignal_sku', 'products.sku');
        }

        if($sku){
            $products = $products->where('products.sku', 'RLIKE', $sku);
        }

        if($supplier){
            $products = $products->where('supplier_products.supplier_id', $supplier);
            $products->join('supplier_products', 'supplier_products.product_id', 'products.id');
        }

        if($category){
            $products = $products->where('product_static_contents.category', 'RLIKE', $category);
            $products->join('product_static_contents', 'product_static_contents.sku', 'products.sku');
        }

        return $products->count();
    }

    public function recoverProduct(Request $request){
        if($request->has('product_ids')){
            $productIds = explode(',', $request->input('product_ids'));

            foreach($productIds as $productId){
                $parentProduct = Product::where('id', $productId)->select('sku')->first();

                DeletedProduct::where('sku', $parentProduct->sku)->delete();

                $childProducts = Product::where('parent_id', $productId)->select('sku')->get();

                foreach($childProducts as $childProduct){
                    DeletedProduct::where('sku', $childProduct->sku)->delete();
                }
            }
        }

        return redirect()->back()->with('message', 'Product has been recovered.');
    }

    public function printingPositions($productId){
        return ProductImprintPosition::where('product_id', $productId)->get();
    }

    public function printingPositionsTextDe($positionId){
        return ProductImprintPositionLocationText::where([['language', 'de'],['product_imprint_position_id', $positionId]])->first();
    }

    public function printingPositionOptions($positionId){
        return ProductImprintPositionOption::where('product_imprint_position_id', $positionId)->get();
    }

    public function printingSellingPrice($optionId){
        return ProductImprintPositionOptionPriceCountryBased::where([['country_currency', 'DEU'], ['type', 'Recommended Selling Price'], ['product_imprint_position_option_id', $optionId]])->get();
    }
}
