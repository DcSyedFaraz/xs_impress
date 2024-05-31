<?php

namespace App\Http\Controllers;

use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class FareController extends Controller
{
    public function convert(){
        $productCreator = new DashboardController();

        $productPrinterCollection = $this->readExcel(public_path()."/suppliers/1/Veredelungspreisliste 2024 - s2.xlsx");
        $productPrinterData = $this->buildExcelDataBy($productPrinterCollection, 'ERP-SKU-ID');

        $printerCollection = $this->readExcel(public_path()."/suppliers/1/Veredelungspreisliste 2024 - s1.xlsx");
        $printerData = $this->buildExcelDataBy($printerCollection, 'Print code');

        $productCollection = $this->readExcel(public_path()."/suppliers/1/FARE_Master_DE.xlsx");

        $parentIds = [];
        $previousParentId = 0;
        $previousCreatedParentId = 0;

        foreach($productCollection as $key => $row){
            $parentIds[] = $row['item number'];

            if($previousParentId != $row['item number']) {
                $previousParentId = $row['item number'];

                $product = $this->buildProductData($row, 'parent', $productPrinterData, $printerData);
                $parentProduct = $productCreator->createProduct($product, 0, 'HashNotAvailable', 2);

                $previousCreatedParentId = $parentProduct->id;

                foreach($product['ProductDetails'] as $language => $productDetails){
                    $productCreator->storeProductDetails($previousCreatedParentId, $language, $productDetails);
                }

                $productCreator->storeProductPrice($previousCreatedParentId, $product);

                $productCreator->storePrintData($previousCreatedParentId, $product);

                $variant = $this->buildProductData($row, 'variant', $productPrinterData, $printerData);
                $variantProduct = $productCreator->createProduct($variant, $previousCreatedParentId, 'HashNotAvailable', 2);

                foreach($variant['ProductDetails'] as $language => $productDetails){
                    $productCreator->storeProductDetails($variantProduct->id, $language, $productDetails);
                }

                $productCreator->storeProductPrice($variantProduct->id, $variant);

                $productCreator->storePrintData($variantProduct->id, $variant);
            }else{
                $variant = $this->buildProductData($row, 'variant', $productPrinterData, $printerData);
                $variantProduct = $productCreator->createProduct($variant, $previousCreatedParentId, 'HashNotAvailable', 2);

                foreach($variant['ProductDetails'] as $language => $productDetails){
                    $productCreator->storeProductDetails($variantProduct->id, $language, $productDetails);
                }

                $productCreator->storeProductPrice($variantProduct->id, $variant);

                $productCreator->storePrintData($variantProduct->id, $variant);
            }
        }


    }

    public function buildProductData($data, $type, $productPrinterData, $printerData){
        $mainTable = [
            'Sku' => $type=='variant'? $data['ERP-SKU-ID'] : $data['ERP-SKU-ID'].'-parent',
            'SupplierSku' => $type=='variant'? $data['ERP-SKU-ID'] : $data['ERP-SKU-ID'].'-parent',
            'ANumber' => '',
            'NonLanguageDependedProductDetails' => [
                'Multicolor (1=yes/0=no)' => $data['Multicolor (1=yes/0=no)'],
                'Pantone color values (approx.)' => $data['Pantone color values (approx.)'],
                'Color values CMYK (approx.)' => $data['Color values CMYK (approx.)'],
                'Shade diameter (cm)' => $data['Shade diameter (cm)'],
                'Shade width (cm)' => $data['Shade width (cm)'],
                'Shade depth (cm)' => $data['Shade depth (cm)'],
                'Closed length (cm)' => $data['Closed length (cm)'],
                'number of wedges' => $data['number of wedges'],
                'Weight (g)' => $data['Weight (g)'],
                'Stick diameter (mm)' => $data['Stick diameter (mm)'],
                'Weight / SKU (g)' => $data['Weight / SKU (g)'],
                'Alternative articles' => $data['Alternative articles'],
                'Customs tariff number' => $data['Customs tariff number'],
                'Country of origin ISO' => $data['Country of origin ISO'],
                'Product detail page' => $data['Product detail page'],
                'EAN inner packaging' => $data['EAN inner packaging'],
                'Weight of inner packaging (g)]' => $data['Weight of inner packaging (g)'],
                'Dimensions of inner packaging (cm)' => $data['Dimensions of inner packaging (cm)'],
                'SKU per inner packaging' => $data['SKU per inner packaging'],
                'EAN outer packaging' => $data['EAN outer packaging'],
                'Dimensions of outer packaging (cm)' => $data['Dimensions of outer packaging (cm)'],
                'Inner packaging per outer packaging' => $data['Inner packaging per outer packaging'],
            ],
            'BatteryInformation' => [],
            'Ean' => $data['EAN SKU'],
            'VideoUrl' => '',
            'ForbiddenRegions' => '',
            'ImprintReferences' => [],
            'ProductCosts' => [],
            'SamplePriceCountryBased' => [],
            'ProductPriceRegionBased' => [],
            'UnstructuredInformation' => [],
            'ProductDetails' => [
                'de' => [
                    'Name' => $data['Item name'],
                    'ShortDescription' => $data['Short description'],
                    'Description' => $data['Description'],
                    'MetaName' => '',
                    'MetaDescription' => '',
                    'MetaKeywords' => '',
                    'IsActive' => 1,
                    'UnstructuredInformation' => [
                        'Product group' => $data['Product group'],
                        'Color abbreviations' => $data['Color abbreviations'],
                        'Search color' => $data['Search color'],
                        'Features' => $data['Features'],
                        'SupplierMainCategory' => ''
                    ],
                    'WebShopInformation' => [
                        'Griffmaterial' => $data['Stockmaterial'],
                        'Material' => $data['Material'],
                        'Features' => $data['Features'],
                        'Reference material' => $data['Reference material'],
                        'Material inner packaging' => $data['Material inner packaging'],
                        'Material outer packaging' => $data['Material outer packaging']
                    ],
                    'ImportantInformation' => [],
                    'PIMV1Information' => '',
                    'Image' => [
                        'Url' => $data['Image reference master'],
                        'Description' => '',
                        'FileName' => $data['Image file master'],
                    ],
                    'MediaGalleryImages' => [
                        [
                            'Url' => $data['Image reference 1'],
                            'Description' => '',
                            'FileName' => $data['Image file 1'],
                        ],
                        [
                            'Url' => $data['Image reference 2'],
                            'Description' => '',
                            'FileName' => $data['Image file 2'],
                        ],
                        [
                            'Url' => $data['Image reference 3'],
                            'Description' => '',
                            'FileName' => $data['Image file 3'],
                        ],
                        [
                            'Url' => $data['Image reference 4'],
                            'Description' => '',
                            'FileName' => $data['Image file 4'],
                        ],
                        [
                            'Url' => $data['Image reference 5'],
                            'Description' => '',
                            'FileName' => $data['Image file 5'],
                        ],
                        [
                            'Url' => $data['Image reference 6'],
                            'Description' => '',
                            'FileName' => $data['Image file 6'],
                        ],
                        [
                            'Url' => $data['Image reference 7'],
                            'Description' => '',
                            'FileName' => $data['Image file 7'],
                        ],
                        [
                            'Url' => $data['Image reference 8'],
                            'Description' => '',
                            'FileName' => $data['Image file 8'],
                        ],
                        [
                            'Url' => $data['Image reference 9'],
                            'Description' => '',
                            'FileName' => $data['Image file 9'],
                        ]
                    ],
                    'ConfigurationFields' => [
                        [
                            'ConfigurationName' => 'Color',
                            'ConfigurationNameTranslated' => '',
                            'ConfigurationValue' => $data['Color'],
                        ],
                        [
                            'ConfigurationName' => 'Size',
                            'ConfigurationNameTranslated' => '',
                            'ConfigurationValue' => $data['Size'],
                        ]
                    ]
                ]
            ],
            'ProductPriceCountryBased' => [
                'DEU' => [
                    'RecommendedSellingPrice' => [
                        [
                            'Price' => $data['Preis 1 (€)']? $data['Preis 1 (€)'] : 0,
                            'Quantity' => $data['Amount: 1']? $data['Amount: 1'] : 0,
                            'OnRequest' => 0,
                            'Valuta' => 'EURO',
                        ],
                        [
                            'Price' => $data['Preis 2 (€)']? $data['Preis 2 (€)'] : 0,
                            'Quantity' => $data['Quantity 2']? $data['Quantity 2'] : 0,
                            'OnRequest' => 0,
                            'Valuta' => 'EURO'
                        ],
                        [
                            'Price' => $data['Preis 3 (€)']?$data['Preis 3 (€)'] : 0,
                            'Quantity' => $data['Quantity 3']? $data['Quantity 3'] : 0,
                            'OnRequest' => 0,
                            'Valuta' => 'EURO'
                        ],
                        [
                            'Price' => $data['Preis 4 (€)']? $data['Preis 4 (€)'] : 0,
                            'Quantity' => $data['Quantity 4']? $data['Quantity 4'] : 0,
                            'OnRequest' => 0,
                            'Valuta' => 'EURO'
                        ],
                        [
                            'Price' => $data['Preis 5 (€)']? $data['Preis 5 (€)'] : 0,
                            'Quantity' => $data['Quantity 5']? $data['Quantity 5'] : 0,
                            'OnRequest' => 0,
                            'Valuta' => 'EURO'
                        ]
                    ],
                    'GeneralBuyingPrice' => [
                        [
                            'Price' => 0,
                            'Quantity' => 0,
                            'OnRequest' => 0,
                            'Valuta' => 'EURO'
                        ],
                        [
                            'Price' => 0,
                            'Quantity' => 0,
                            'OnRequest' => 0,
                            'Valuta' => 'EURO'
                        ],
                        [
                            'Price' => 0,
                            'Quantity' => 0,
                            'OnRequest' => 0,
                            'Valuta' => 'EURO'
                        ],
                        [
                            'Price' => 0,
                            'Quantity' => 0,
                            'OnRequest' => 0,
                            'Valuta' => 'EURO'
                        ],
                        [
                            'Price' => 0,
                            'Quantity' => 0,
                            'OnRequest' => 0,
                            'Valuta' => 'EURO'
                        ],
                    ],
                    'QuantityIncrements' => 1,
                    'VatPercentage' => 0,
                    'MinimumOrderQuantity' => 1,
                    'VatSettingId' => 0
                ]
            ],

        ];

        try{
            $printGroups = explode(',', (string)$productPrinterData[$data['ERP-SKU-ID']]['Print code']);
        }catch (\Exception $exception){
            $mainTable['ImprintPositions'] = null;
            return $mainTable;
        }

        $imprintText = [];

        foreach($printGroups as $groupId){
            if(!isset($printerData[$groupId])){
                continue;
            }

            $currentRow = $printerData[$groupId];

            $imprintText['de'] = [
                'Images' => [
                    [
                        'Url' => '',
                        'Description' => '',
                        'FileName' => '',
                    ]
                ],
                'Name' => 'Auf Produckt',
                'Description' => '',
            ];

            $mainTable['ImprintPositions'] = [
                [
                    'PositionCode' => 'auf_product',
                    'UnstructuredInformation' => [
                        'MaxPrintArea' => ''
                    ],
                    'ImprintLocationTexts' => $imprintText,
                    'ImprintOptions' => [
                        [
                            'ChildImprints' => [],
                            'PrintColorAsText' => [],
                            'Dimension' => '',
                            'ImprintTexts' => [
                                'de' => [
                                    'Name' => $currentRow['Name'],
                                    'Description' => $currentRow['Description']
                                ]
                            ],
                            'Sku' => '',
                            'SupplierSku' => '',
                            'DimensionsHeight' => 0,
                            'DimensionsDiameter' => 0,
                            'DimensionsWidth' => 0,
                            'DimensionsDepth' => 0,
                            'ImprintType' => '',
                            'UnstructuredInformation' => [],
                            'PrintColor' => $currentRow['Colors']? $currentRow['Colors'] : 0,
                            'IsActiveRegionBased' => [],
                            'IsActiveCountryBased' => [],
                            'ImportantInformation' => [],
                            'ProductPriceRegionBased' => [],
                            'ProductPriceCountryBased' => [
                                'DEU' => [
                                    'RecommendedSellingPrice' => [
                                        [
                                            'Price' => isset($currentRow['Season price1'])? $currentRow['Season price1'] : 0,
                                            'Quantity' => isset($currentRow['Season quantity1'])? $currentRow['Season quantity1'] : 0,
                                            'OnRequest' => 0,
                                            'Valuta' => 'EURO',
                                        ],
                                    ],
                                    'GeneralBuyingPrice' => [
                                        [
                                            'Price' => 0,
                                            'Quantity' => 0,
                                            'OnRequest' => 0,
                                            'Valuta' => 'EURO',
                                        ],
                                    ],
                                    'QuantityIncrements' => 1,
                                    'VatPercentage' => 0,
                                    'MinimumOrderQuantity' => 0,
                                    'VatSettingId' => 0
                                ]
                            ],
                            'ImprintCosts' => [
                                [
                                    'Sku' => '',
                                    'SupplierSku' => '',
                                    'Texts' => [],
                                    'ProductPriceRegionBased' => [],
                                    'ProductPriceCountryBased' => [],
                                    'IsActiveRegionBased' => [],
                                    'IsActiveCountryBased' => [],
                                    'CalculationType' => '',
                                    'CalculationAmount' => '',
                                    'Requirement' => [],
                                    'UnstructuredInformation' => [],
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }

        return $mainTable;
    }

    public function readExcel($file){
        return (new FastExcel)->import($file);
    }

    public function buildExcelDataBy($collection, $key){
        $data = [];

        foreach($collection as $row){
            $data[$row[$key]] = $row;
        }

        return $data;
    }
}
