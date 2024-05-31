<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CustomProductSku;
use App\Models\Notification;
use App\Models\Product;
use App\Models\ProductConfiguration;
use App\Models\ProductDetail;
use App\Models\ProductImage;
use App\Models\ProductImprintPosition;
use App\Models\ProductImprintPositionLocationText;
use App\Models\ProductImprintPositionOption;
use App\Models\ProductImprintPositionOptionCost;
use App\Models\ProductImprintPositionOptionPriceCountryBased;
use App\Models\ProductMediaGalleryImage;
use App\Models\ProductPriceCountryBased;
use App\Models\ProductStaticContent;
use App\Http\Requests\CreateCategory;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function dashboard(){
        return view('dashboard');
    }

    public function index(){
        $myfile = fopen("https://promidatabase.s3.eu-central-1.amazonaws.com/Profiles/Live/9ccfda5a-8127-495a-a089-5df05b2c7a11/Import/Import.txt", "r") or die("Unable to open file!");

        while(!feof($myfile)) {
            $productFile = fgets($myfile);
            if(str_contains($productFile, '.json')){
                $splitUrl = explode('|', $productFile);
                $productUrl = $splitUrl[0];
                $productHash = trim($splitUrl[1]);

                $breakUrl = explode('/', $productUrl);
                $skuWithJson = $breakUrl[count($breakUrl) - 1];
                $sku = str_replace('.json', '', $skuWithJson);
                $checkProduct = Product::where('sku', $sku)->first();

                if($checkProduct){
                    if($checkProduct->hash == $productHash){
                        continue;
                    }else{
                        Product::where('id', $checkProduct->id)->delete();
                        Product::where('parent_id', $checkProduct->id)->delete();

                        Notification::create([
                            'type' => 'Product',
                            'content' => 'Product '.$sku.' has been updated',
                            'model_id' => 0,
                            'read' => 0
                        ]);

                        echo 'Product '.$sku.' removed for re-import / ';
                    }
                }

                $product = json_decode(file_get_contents($productUrl), true);

                // Create Parent Product
                $parentProduct = $this->createProduct($product, 0, $productHash);

                // Loop Product Details Languages
                foreach($product['ProductDetails'] as $language => $productDetails){
                    // Store Product Details
                    $this->storeProductDetails($parentProduct->id, $language, $productDetails);
                }

                // Store Product Prices
                $this->storeProductPrice($parentProduct->id, $product);

                // Store Product Print Data
                $this->storePrintData($parentProduct->id, $product);

                // Loop Child Products
                foreach($product['ChildProducts'] as $variant){
                    // Create Variant Product
                    $variantProduct = $this->createProduct($variant, $parentProduct->id, '');

                    // Loop Variant Details Languages
                    foreach($variant['ProductDetails'] as $language => $variantDetails){
                        // Store Variant Details
                        $this->storeProductDetails($variantProduct->id, $language, $variantDetails);
                    }

                    // Store Variant Prices
                    $this->storeProductPrice($variantProduct->id, $variant);

                    // Store Variant Print Data
                    $this->storePrintData($variantProduct->id, $variant);
                }

                echo $sku." has been imported / ";
            }
        }
        fclose($myfile);

//        $product = json_decode(file_get_contents(public_path().'/Configurable.json'), true);
    }

    public function createProduct($product, $parentId = 0, $hash = '', $supplierId = 1){
        $parentStartSequence = 10001000;
        $childStartSequence = 100;

        $supplier = Supplier::where('id', $supplierId)->first();

        $lastParentProductSequence = Product::where([['supplier_products.supplier_id', $supplierId], ['type', 'parent']])
            ->join('supplier_products', 'supplier_products.product_id', 'products.id')
            ->select('products.supplier_sequence as value')
            ->get()
            ->last();

        $lastChildProductSequence = Product::where([['supplier_products.supplier_id', $supplierId], ['parent_id', $parentId]])
            ->join('supplier_products', 'supplier_products.product_id', 'products.id')
            ->select('products.supplier_sequence as value')
            ->get()
            ->last();

        $sequence = 0;

        if(!$parentId){
            $sequence = $lastParentProductSequence? $lastParentProductSequence->value : $parentStartSequence;
        }else{
            $sequence = $lastChildProductSequence? $lastChildProductSequence->value : $childStartSequence;
        }

        $parent = Product::create([
            'hash' => $hash,
            'supplier_sequence' => $sequence+1,
            'type' => $parentId? 'variant' : 'parent',
            'parent_id' => $parentId,
            'sku' => $product['Sku'],
            'supplier_sku' => $product['SupplierSku'],
            'a_number' => $product['ANumber'],
            'non_language_depended_product_details' => json_encode($product['NonLanguageDependedProductDetails']),
            'battery_information' => json_encode($product['BatteryInformation']),
            'ean' => $product['Ean'],
            'video_url' => $product['VideoUrl'],
            'forbidden_regions' => $product['ForbiddenRegions'],
            'imprint_references' => isset($product['ImprintReferences'])? json_encode($product['ImprintReferences']) : null,
            'product_costs' => json_encode($product['ProductCosts']),
            'sample_price_country_based' => json_encode($product['SamplePriceCountryBased']),
            'product_price_region_based' => json_encode($product['ProductPriceRegionBased']),
            'unstructured_information' => json_encode($product['UnstructuredInformation']),
        ]);

         SupplierProduct::create([
            'supplier_id' => $supplierId,
            'product_id' => $parent->id
         ]);


        $parentProductSequence = Product::where('id', $parentId? $parentId : $parent->id)->first();
        $childProductSequence = Product::where([['parent_id', $parentId], ['type', 'variant']])->get()->last();

        $finalPSeq = $parentProductSequence->supplier_sequence;
        $finalCSeq = $childProductSequence? $childProductSequence->supplier_sequence : $childStartSequence;

        $customSku = 'XS'.$finalPSeq.'.'.$finalCSeq.'-'.$supplier->supplier_code;

        $checkCustomSku = CustomProductSku::where('orignal_sku', $product['Sku'])->first();

//        if(!$checkCustomSku){
        try{
            CustomProductSku::create([
                'orignal_sku' => $product['Sku'],
                'custom_sku' => $customSku
            ]);
        }catch (\Exception $exception){

        }

//        }

        return $parent;
    }

    public function storeProductDetails($id, $language, $product){
        $_product = Product::find($id);

        $productStaticContent = ProductStaticContent::where([['sku', $_product->sku], ['language', $language]])->first();

        if(!$productStaticContent){
            ProductStaticContent::create([
                'sku' => $_product->sku,
                'language' => $language,
                'category' => isset(json_decode($_product->non_language_depended_product_details, true)['Category'])? json_decode($_product->non_language_depended_product_details, true)['Category'] : '',
                'description' => $product['Description']
            ]);
        }

        $productDetail = ProductDetail::create([
            'product_id' => $id,
            'language' => $language,
            'name' => $product['Name'],
            'description' => $product['Description'],
            'short_description' => $product['ShortDescription'],
            'meta_name' => $product['MetaName'],
            'meta_description' => $product['MetaDescription'],
            'meta_keywords' => $product['MetaKeywords'],
            'is_active' => $product['IsActive'],
            'unstructured_information' => json_encode($product['UnstructuredInformation']),
            'web_shop_information' => json_encode($product['WebShopInformation']),
            'important_information' => json_encode($product['ImportantInformation']),
            'pimv1_information' => $product['PIMV1Information']
        ]);

        ProductImage::create([
            'product_detail_id' => $productDetail->id,
            'url' => $product['Image']['Url'],
            'description' => $product['Image']['Description'],
            'file_name' => $product['Image']['FileName'],
        ]);

        $jsonImages = [];

        if($product['MediaGalleryImages']){
            foreach($product['MediaGalleryImages'] as $mediaGalleryImage){
                ProductMediaGalleryImage::create([
                    'product_detail_id' => $productDetail->id,
                    'url' => $mediaGalleryImage['Url'],
                    'description' => $mediaGalleryImage['Description'],
                    'file_name' => $mediaGalleryImage['FileName'],
                ]);

                $jsonImages[] = $mediaGalleryImage['Url'];
            }
        }

        if($product['ConfigurationFields']){
            foreach($product['ConfigurationFields'] as $configurationField){
                ProductConfiguration::create([
                    'product_detail_id' => $productDetail->id,
                    'name' => $configurationField['ConfigurationName'],
                    'name_translated' => $configurationField['ConfigurationNameTranslated'],
                    'value' => $configurationField['ConfigurationValue'],
                ]);
            }
        }

        $customProductSkuData = CustomProductSku::where('orignal_sku', $_product->sku)->first();

        if($language == 'de'){
            $jsonFile = [
                "SKU" => $customProductSkuData->custom_sku,
                "ProductName" => $product['Name'],
                "Description" => $product['Description'],
                "PictureURLs" => $jsonImages
            ];

            $filePath = public_path().'storage/export/'.$customProductSkuData->custom_sku.'.json';

            if(file_exists($filePath)){
                unlink($filePath);
            }

            Storage::disk('public')->put('export/'.$customProductSkuData->custom_sku.'.json', json_encode($jsonFile));
        }
    }

    public function storeProductPrice($id, $product){
        if($product['ProductPriceCountryBased']){
            foreach($product['ProductPriceCountryBased'] as $countryCurrency => $priceCountryBased){
                foreach($priceCountryBased['RecommendedSellingPrice'] as $recommendedSellingPrice){
                    ProductPriceCountryBased::create([
                        'product_id' => $id,
                        'country_currency' => $countryCurrency,
                        'type' => 'Recommended Selling Price',
                        'price' => $recommendedSellingPrice['Price'],
                        'quantity' => $recommendedSellingPrice['Quantity'],
                        'on_request' => $recommendedSellingPrice['OnRequest'],
                        'valuta' => $recommendedSellingPrice['Valuta'],
                        'quantity_increments' => $priceCountryBased['QuantityIncrements'],
                        'vat_percentage' => $priceCountryBased['VatPercentage'],
                        'minimum_order_quantity' => $priceCountryBased['MinimumOrderQuantity'],
                        'vat_setting_id' => $priceCountryBased['VatSettingId'],
                    ]);
                }

                foreach($priceCountryBased['GeneralBuyingPrice'] as $generalBuyingPrice){
                    ProductPriceCountryBased::create([
                        'product_id' => $id,
                        'country_currency' => $countryCurrency,
                        'type' => 'General Buying Price',
                        'price' => $generalBuyingPrice['Price'],
                        'quantity' => $generalBuyingPrice['Quantity'],
                        'on_request' => $generalBuyingPrice['OnRequest'],
                        'valuta' => $generalBuyingPrice['Valuta'],
                        'quantity_increments' => $priceCountryBased['QuantityIncrements'],
                        'vat_percentage' => $priceCountryBased['VatPercentage'],
                        'minimum_order_quantity' => $priceCountryBased['MinimumOrderQuantity'],
                        'vat_setting_id' => $priceCountryBased['VatSettingId'],
                    ]);
                }
            }
        }
    }

    public function storePrintData($id, $product){
        if(isset($product['ImprintPositions']) && $product['ImprintPositions']){
            foreach($product['ImprintPositions'] as $imprintPosition){
                $position = ProductImprintPosition::create([
                    'product_id' => $id,
                    'position_code' => $imprintPosition['PositionCode'],
                    'unstructured_information' => json_encode($imprintPosition['UnstructuredInformation'])
                ]);

                foreach($imprintPosition['ImprintLocationTexts'] as $language => $imprintLocationText){
                    ProductImprintPositionLocationText::create([
                        'product_imprint_position_id' => $position->id,
                        'language' => $language,
                        'images' => json_encode($imprintLocationText['Images']),
                        'name' => $imprintLocationText['Name'],
                        'description' => $imprintLocationText['Description']
                    ]);
                }

                foreach($imprintPosition['ImprintOptions'] as $imprintOption){
                    $productImprintPositionOption = ProductImprintPositionOption::create([
                        'product_imprint_position_id' => $position->id,
                        'child_imprints' => json_encode($imprintOption['ChildImprints']),
                        'print_color_as_text' => json_encode($imprintOption['PrintColorAsText']),
                        'dimension' => $imprintOption['Dimension'],
                        'imprint_texts' => json_encode($imprintOption['ImprintTexts']),
                        'sku' => $imprintOption['Sku'],
                        'supplier_sku' => $imprintOption['SupplierSku'],
                        'dimensions_height' => $imprintOption['DimensionsHeight'],
                        'dimensions_diameter' => $imprintOption['DimensionsDiameter'],
                        'dimensions_width' => $imprintOption['DimensionsWidth'],
                        'dimensions_depth' => $imprintOption['DimensionsDepth'],
                        'imprint_type' => $imprintOption['ImprintType'],
                        'unstructured_information' => json_encode($imprintOption['UnstructuredInformation']),
                        'print_color' => $imprintOption['PrintColor'],
                        'is_active_region_based' => json_encode($imprintOption['IsActiveRegionBased']),
                        'is_active_country_based' => json_encode($imprintOption['IsActiveCountryBased']),
                        'important_information' => json_encode($imprintOption['ImportantInformation']),
                        'price_region_based' => json_encode($imprintOption['ProductPriceRegionBased'])
                    ]);

                    foreach($imprintOption['ProductPriceCountryBased'] as $currency => $priceCountryBased){
                        foreach($priceCountryBased['RecommendedSellingPrice'] as $recommendedSellingPrice){
                            if($recommendedSellingPrice['Price'] == ''){
                                continue;
                            }
                            ProductImprintPositionOptionPriceCountryBased::create([
                                'product_imprint_position_option_id' => $productImprintPositionOption->id,
                                'country_currency' => $currency,
                                'type' => 'Recommended Selling Price',
                                'price' => $recommendedSellingPrice['Price'],
                                'quantity' => $recommendedSellingPrice['Quantity'],
                                'on_request' => $recommendedSellingPrice['OnRequest'],
                                'valuta' => $recommendedSellingPrice['Valuta'],
                                'quantity_increments' => $priceCountryBased['QuantityIncrements'],
                                'vat_percentage' => $priceCountryBased['VatPercentage'],
                                'minimum_order_quantity' => $priceCountryBased['MinimumOrderQuantity'],
                                'vat_setting_id' => $priceCountryBased['VatSettingId']
                            ]);
                        }

                        foreach($priceCountryBased['GeneralBuyingPrice'] as $generalBuyingPrice){
                            if($generalBuyingPrice['Price'] == ''){
                                continue;
                            }
                            ProductImprintPositionOptionPriceCountryBased::create([
                                'product_imprint_position_option_id' => $productImprintPositionOption->id,
                                'country_currency' => $currency,
                                'type' => 'General Buying Price',
                                'price' => $generalBuyingPrice['Price'],
                                'quantity' => $generalBuyingPrice['Quantity'],
                                'on_request' => $generalBuyingPrice['OnRequest'],
                                'valuta' => $generalBuyingPrice['Valuta'],
                                'quantity_increments' => $priceCountryBased['QuantityIncrements'],
                                'vat_percentage' => $priceCountryBased['VatPercentage'],
                                'minimum_order_quantity' => $priceCountryBased['MinimumOrderQuantity'],
                                'vat_setting_id' => $priceCountryBased['VatSettingId']
                            ]);
                        }
                    }

                    if($imprintOption['ImprintCosts']){
                        foreach($imprintOption['ImprintCosts'] as $imprintCost){
                            ProductImprintPositionOptionCost::create([
                                'product_imprint_position_option_id' => $productImprintPositionOption->id,
                                'sku' => $imprintCost['Sku'],
                                'supplier_sku' => $imprintCost['SupplierSku'],
                                'texts' => json_encode($imprintCost['Texts']),
                                'price_region_based' => json_encode($imprintCost['ProductPriceRegionBased']),
                                'price_country_based' => json_encode($imprintCost['ProductPriceCountryBased']),
                                'is_active_region_based' => json_encode($imprintCost['IsActiveRegionBased']),
                                'is_active_country_based' => json_encode($imprintCost['IsActiveCountryBased']),
                                'calculation_type' => $imprintCost['CalculationType'],
                                'calculation_amount' => $imprintCost['CalculationAmount'],
                                'requirement' => json_encode($imprintCost['Requirement']),
                                'unstructured_information' => json_encode($imprintCost['UnstructuredInformation'])
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function export(Request $request){
        if(!$request->has('limit') || !$request->has('skip')){
            return 'Please set skip and limit';
        }

        $skip = $request->skip;
        $limit = $request->limit;

        $parentProducts = Product::where('parent_id', 0)->skip($skip)->take($limit)->get();

        $productJson = [];

        $productCounter = 0;

        foreach($parentProducts as $parentProduct){
            $productJson[$productCounter]['product'] = $this->getProductData($parentProduct);

            $childProducts = Product::where('parent_id', $parentProduct->id)
                ->select('id', 'sku', 'supplier_sku', 'a_number', 'ean', 'video_url', 'non_language_depended_product_details')
                ->get();

            foreach($childProducts as $childProduct) {
                $productJson[$productCounter]['variants'][] = $this->getProductData($childProduct);
            }

            $productCounter++;
        }

        return json_encode($productJson);
    }

    public function getProductData($childProduct){
        $productJson = [];

        $customSku =  CustomProductSku::where('orignal_sku', $childProduct->sku)->first();

        $productJson['categories'] = isset(json_decode($childProduct->non_language_depended_product_details, true)['Category'])? json_decode($childProduct->non_language_depended_product_details, true)['Category'] : '';
        $productJson['sku'] = $childProduct->sku;
        $productJson['supplier_sku'] = $childProduct->supplier_sku;
        $productJson['xs_sku'] = $customSku->custom_sku;
        $productJson['a_number'] = $childProduct->a_number;
        $productJson['ean'] = $childProduct->ean;
        $productJson['video_url'] = $childProduct->video_url;
        $productJson['non_language_dependent_details'] = json_decode($childProduct->non_language_depended_product_details, true);

        $languages = ProductDetail::where('product_id', $childProduct->id)->groupBy('language')->select('language')->get();

        foreach($languages as $language){
            $languageDependentDetails = ProductDetail::where([['product_id', $childProduct->id], ['language', $language->language]])
                ->select('id', 'name', 'description', 'short_description', 'meta_name', 'meta_description', 'meta_keywords', 'is_active', 'web_shop_information')
                ->first();

            $productJson['language_dependent_details'][$language->language]['name'] =  $languageDependentDetails->name;
            $productJson['language_dependent_details'][$language->language]['short_description'] =  $languageDependentDetails->short_description;
            $productJson['language_dependent_details'][$language->language]['meta_name'] =  $languageDependentDetails->meta_name;
            $productJson['language_dependent_details'][$language->language]['meta_description'] =  $languageDependentDetails->meta_description;
            $productJson['language_dependent_details'][$language->language]['meta_keywords'] =  $languageDependentDetails->meta_keywords;
            $productJson['language_dependent_details'][$language->language]['is_active'] =  $languageDependentDetails->is_active;
            $productJson['language_dependent_details'][$language->language]['web_shop_information'] =  json_decode($languageDependentDetails->web_shop_information, true);

            $image = ProductImage::where('product_detail_id', $languageDependentDetails->id)->select('url', 'description')->first();
            $mediaGallery = ProductMediaGalleryImage::where('product_detail_id', $languageDependentDetails->id)->select('url', 'description')->get();

            $productJson['images'][] = ['url' => $image->url, 'description' => $image->description];

            foreach($mediaGallery as $galleryImage){
                $productJson['images'][] = ['url' => $galleryImage->url, 'description' => $galleryImage->description];
            }
        }

        $countryCurrency = ProductPriceCountryBased::where('product_id', $childProduct->id)->groupBy('country_currency')->select('country_currency')->get();

        foreach($countryCurrency as $currency){
            $productPrices = ProductPriceCountryBased::where([['product_id', $childProduct->id], ['country_currency', $currency->country_currency], ['type', 'Recommended Selling Price']])
                ->select('id', 'price', 'quantity', 'on_request', 'valuta', 'quantity_increments', 'vat_percentage', 'minimum_order_quantity')
                ->get();

            foreach($productPrices as $productPrice){
                $productJson['product_price'][$currency->country_currency][$productPrice->quantity]['quantity'] = $productPrice->quantity;
                $productJson['product_price'][$currency->country_currency][$productPrice->quantity]['price'] = $productPrice->price;
                $productJson['product_price'][$currency->country_currency][$productPrice->quantity]['on_request'] = $productPrice->on_request;
                $productJson['product_price'][$currency->country_currency][$productPrice->quantity]['valuta'] = $productPrice->valuta;
                $productJson['product_price'][$currency->country_currency][$productPrice->quantity]['quantity_increments'] = $productPrice->quantity_increments;
                $productJson['product_price'][$currency->country_currency][$productPrice->quantity]['vat_percentage'] = $productPrice->vat_percentage;
                $productJson['product_price'][$currency->country_currency][$productPrice->quantity]['minimum_order_quantity'] = $productPrice->minimum_order_quantity;
            }
        }

        $printPositions = ProductImprintPosition::where('product_id', $childProduct->id)
            ->select('id', 'unstructured_information', 'position_code')
            ->get();

        foreach($printPositions as $printPosition){
            $locationTexts = ProductImprintPositionLocationText::where('product_imprint_position_id', $printPosition->id)->get();

            foreach($locationTexts as $locationText){
                $productJson['printing']['positions'][$printPosition->position_code][$locationText->language] = ['name' => $locationText->name, 'description' => $locationText->description, 'images' => json_decode($locationText->images, true)];
            }

            $printOptions = ProductImprintPositionOption::where('product_imprint_position_id', $printPosition->id)
                ->select('id', 'imprint_texts', 'sku')
                ->get();

            foreach($printOptions as $printOption){
                $imprintOptionText = json_decode($printOption->imprint_texts, true);

                if(is_array($imprintOptionText)){
                    foreach($imprintOptionText as $language => $text){
                        $productJson['printing']['positions'][$printPosition->position_code]['options'][$printOption->sku][$language]['name'] = $text['Name'];
                    }
                }

                $printPricesCountryCurrency = ProductImprintPositionOptionPriceCountryBased::where('product_imprint_position_option_id', $printOption->id)->select('country_currency')->groupBy('country_currency')->get();

                foreach($printPricesCountryCurrency as $printPricesCurrency){
                    $printOptionPrices = ProductImprintPositionOptionPriceCountryBased::where([['product_imprint_position_option_id', $printOption->id], ['country_currency', $printPricesCurrency->country_currency], ['type', 'Recommended Selling Price']])
                        ->select('price', 'quantity', 'on_request', 'valuta', 'quantity_increments', 'vat_percentage', 'minimum_order_quantity')
                        ->get();

                    foreach($printOptionPrices as $printOptionPrice){
                        $productJson['printing']['positions'][$printPosition->position_code]['options'][$printOption->sku]['price'][$printPricesCurrency->country_currency][$printOptionPrice->quantity]['quantity'] = $printOptionPrice->quantity;
                        $productJson['printing']['positions'][$printPosition->position_code]['options'][$printOption->sku]['price'][$printPricesCurrency->country_currency][$printOptionPrice->quantity]['price'] = $printOptionPrice->price;
                        $productJson['printing']['positions'][$printPosition->position_code]['options'][$printOption->sku]['price'][$printPricesCurrency->country_currency][$printOptionPrice->quantity]['on_request'] = $printOptionPrice->on_request;
                        $productJson['printing']['positions'][$printPosition->position_code]['options'][$printOption->sku]['price'][$printPricesCurrency->country_currency][$printOptionPrice->quantity]['valuta'] = $printOptionPrice->valuta;
                        $productJson['printing']['positions'][$printPosition->position_code]['options'][$printOption->sku]['price'][$printPricesCurrency->country_currency][$printOptionPrice->quantity]['quantity_increments'] = $printOptionPrice->quantity_increments;
                        $productJson['printing']['positions'][$printPosition->position_code]['options'][$printOption->sku]['price'][$printPricesCurrency->country_currency][$printOptionPrice->quantity]['vat_percentage'] = $printOptionPrice->vat_percentage;
                        $productJson['printing']['positions'][$printPosition->position_code]['options'][$printOption->sku]['price'][$printPricesCurrency->country_currency][$printOptionPrice->quantity]['minimum_order_quantity'] = $printOptionPrice->minimum_order_quantity;
                    }
                }

                $printOptionCosts = ProductImprintPositionOptionCost::where('product_imprint_position_option_id', $printOption->id)
                    ->select('price_country_based')
                    ->get();

                foreach($printOptionCosts as $printOptionCost){
                    $oneTimeCost = json_decode($printOptionCost->price_country_based, true);

                    if(is_array($oneTimeCost)){
                        foreach($oneTimeCost as $currency => $otCost){
                            $productJson['printing']['positions'][$printPosition->position_code]['options'][$printOption->sku]['one_time_cost'][$currency]['cost'] = isset($otCost['RecommendedSellingPrice'][0]['Price'])? $otCost['RecommendedSellingPrice'][0]['Price'] : 0;
                        }
                    }
                }
            }
        }

        $configuration = ProductConfiguration::where('product_detail_id', $languageDependentDetails->id)->get();

        foreach($configuration as $swatch){
            $productJson['configuration'][][$swatch->name] = $swatch->value;
        }

        return $productJson;
    }

    public function notifications(){
        return Notification::where('read', 0)->get();
    }

    public function getNotifications(){
        $notifications = $this->notifications();

        Notification::where('id', '>', 0)->update([
           'read' => 1
        ]);

        return view('notification', compact(['notifications']));
    }
}
