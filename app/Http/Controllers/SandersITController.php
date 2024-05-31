<?php

namespace App\Http\Controllers;

use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class SandersITController extends Controller
{
    public function convert(){
        $productCreator = new DashboardController();

        $printerCollection = $this->readExcel(public_path()."/suppliers/2/Sanders IT 2024 - Druck- und Nebenkosten.xlsx");
        $printerData = $this->buildExcelDataBy($printerCollection, 'Print group');

        $productCollection = $this->readExcel(public_path()."/suppliers/2/Sanders_IT_2024_Artikeldaten_DE.xlsx");

        $parentIds = [];
        $previousProductGroup = '';
        $previousCreatedParentId = 0;

        foreach($productCollection as $key => $row){
            $articleNumber = explode('-', $row['Article no.']);
            unset($articleNumber[3]);

            $parentArticleNumber = implode('-', $articleNumber);

            $parentIds[] = $parentArticleNumber;

            if($previousProductGroup != $parentArticleNumber) {
                $previousProductGroup = $parentArticleNumber;

                $product = $this->buildProductData($row, 'parent', $printerData);
                $parentProduct = $productCreator->createProduct($product, 0, 'HashNotAvailable', 3);

                $previousCreatedParentId = $parentProduct->id;

                foreach($product['ProductDetails'] as $language => $productDetails){
                    $productCreator->storeProductDetails($previousCreatedParentId, $language, $productDetails);
                }

                $productCreator->storeProductPrice($previousCreatedParentId, $product);

                $productCreator->storePrintData($previousCreatedParentId, $product);

                $variant = $this->buildProductData($row, 'variant', $printerData);
                $variantProduct = $productCreator->createProduct($variant, $previousCreatedParentId, 'HashNotAvailable', 3);

                foreach($variant['ProductDetails'] as $language => $productDetails){
                    $productCreator->storeProductDetails($variantProduct->id, $language, $productDetails);
                }

                $productCreator->storeProductPrice($variantProduct->id, $variant);

                $productCreator->storePrintData($variantProduct->id, $variant);
            }else{
                $variant = $this->buildProductData($row, 'variant', $printerData);
                $variantProduct = $productCreator->createProduct($variant, $previousCreatedParentId, 'HashNotAvailable', 3);

                foreach($variant['ProductDetails'] as $language => $productDetails){
                    $productCreator->storeProductDetails($variantProduct->id, $language, $productDetails);
                }

                $productCreator->storeProductPrice($variantProduct->id, $variant);

                $productCreator->storePrintData($variantProduct->id, $variant);
            }

//            if($key == 5){
//                break;
//            }
        }


    }

    public function buildProductData($data, $type, $printerData){
        $mainTable = [
            'Sku' => $type=='variant'? $data['Article no.'] : $data['Article no.'].'-parent',
            'SupplierSku' => $type=='variant'? $data['Article no.'] : $data['Article no.'].'-parent',
            'ANumber' => '',
            'NonLanguageDependedProductDetails' => [
                'VAT UK' => $data['VAT UK'],
                'Advance costs EK' => $data['Advance costs EK'],
                'Link 360-Grad' => $data['Link 360-Grad'],
                'Filling quantity' => $data['Filling quantity'],
                'Weight (kg) pcs' => $data['Weight (kg) pcs'],
                'Weight (g) pcs' => $data['Weight (g) pcs'],
                'Dimensions item mm' => $data['Dimensions item mm'],
                'Width items mm' => $data['Width items mm'],
                'Depth item mm' => $data['Depth item mm'],
                'Height item mm' => $data['Height item mm'],
                'Dimensions item cm' => $data['Dimensions item cm'],
                'Width item cm' => $data['Width item cm'],
                'Depth item cm' => $data['Depth item cm'],
                'Height item cm' => $data['Height item cm'],
                'Cylindrical' => $data['Diameter mm'],
                'Diameter cm' => $data['Diameter cm'],
                'VE' => $data['VE'],
                'PU weight g' => $data['PU weight g'],
                'Pack weight kg' => $data['Pack weight kg'],
                'Pack dimensions mm' => $data['Pack dimensions mm'],
                'Pack dimensions cm' => $data['Pack dimensions cm'],
                'Pack width mm' => $data['Pack width mm'],
                'Pack depth mm' => $data['Pack depth mm'],
                'Pack height mm' => $data['Pack height mm'],
                'Pack width cm' => $data['Pack width cm'],
                'Pack depth cm' => $data['Pack depth cm'],
                'Pack height cm' => $data['Pack height cm'],
                'VE pro Palette' => $data['VE pro Palette'],
                'Article pro Palette' => $data['Article pro Palette'],
                'Weight pallet kg' => $data['Weight pallet kg'],
                'Price unit' => $data['Price unit'],
            ],
            'BatteryInformation' => [],
            'Ean' => '',
            'VideoUrl' => $data['Video 1'],
            'ForbiddenRegions' => '',
            'ImprintReferences' => [],
            'ProductCosts' => [],
            'SamplePriceCountryBased' => [],
            'ProductPriceRegionBased' => [],
            'UnstructuredInformation' => [],
            'ProductDetails' => [
                'de' => [
                    'Name' => $data['Article name long DE'],
                    'ShortDescription' => $data['Description DE - HTML formatting'],
                    'Description' => $data['Description DE - HTML formatting'],
                    'MetaName' => '',
                    'MetaDescription' => '',
                    'MetaKeywords' => '',
                    'IsActive' => 1,
                    'UnstructuredInformation' => [
                        'grouping' => $data['grouping'],
                        'Main article grouping' => $data['Main article grouping'],
                        'Pressure group' => $data['“Pressure group'],
                        'Main category' => $data['Main category'],
                        'Subcategory' => $data['Subcategory'],
                        'Marke' => $data['Marke'],
                        'Search terms' => $data['Search terms'],
                        'Sustainable item' => $data['Sustainable item'],
                        'Bio-Article' => $data['Bio-Article'],
                        'Vegan' => $data['Vegan'],
                        'Sugar free' => $data['Sugar free'],
                        'Catalog page' => $data['Catalog page'],
                    ],
                    'WebShopInformation' => [
                        'Delivery time standard' => $data['Delivery time standard'],
                        'Materials DE' => $data['Materials DE'],
                        'Shipping cost group' => $data['Shipping cost group'],
                    ],
                    'ImportantInformation' => [],
                    'PIMV1Information' => '',
                    'Image' => [
                        'Url' => $data['Bild 1'],
                        'Description' => '',
                        'FileName' => $data['Bild 1'],
                    ],
                    'MediaGalleryImages' => [
                        [
                            'Url' => $data['Bild 2'],
                            'Description' => '',
                            'FileName' => $data['Bild 2'],
                        ],
                        [
                            'Url' => $data['Bild 3'],
                            'Description' => '',
                            'FileName' => $data['Bild 3'],
                        ],
                        [
                            'Url' => $data['Bild 4'],
                            'Description' => '',
                            'FileName' => $data['Bild 4'],
                        ],
                        [
                            'Url' => $data['Bild 5'],
                            'Description' => '',
                            'FileName' => $data['Bild 5'],
                        ],
                        [
                            'Url' => $data['Bild 6'],
                            'Description' => '',
                            'FileName' => $data['Bild 6'],
                        ],
                        [
                            'Url' => $data['Bild 7'],
                            'Description' => '',
                            'FileName' => $data['Bild 7'],
                        ],
                        [
                            'Url' => $data['Bild 8'],
                            'Description' => '',
                            'FileName' => $data['Bild 8'],
                        ],
                        [
                            'Url' => $data['Bild 9'],
                            'Description' => '',
                            'FileName' => $data['Bild 9'],
                        ]
                    ],
                    'ConfigurationFields' => [
                        [
                            'ConfigurationName' => $data['Label grouping'],
                            'ConfigurationNameTranslated' => '',
                            'ConfigurationValue' => $data['DE option'],
                        ]
                    ]
                ]
            ],
            'ProductPriceCountryBased' => [
                'DEU' => [
                    'RecommendedSellingPrice' => [
                        [
                            'Price' => $data['VK 1'],
                            'Quantity' => $data['Amount: 1'],
                            'OnRequest' => 0,
                            'Valuta' => 'EURO',
                        ],
                        [
                            'Price' => $data['VK 2']? $data['VK 2'] : 0,
                            'Quantity' => $data['Quantity 2']? $data['Quantity 2'] : 0,
                            'OnRequest' => 0,
                            'Valuta' => 'EURO'
                        ],
                        [
                            'Price' => $data['VK 3']? $data['VK 3'] : 0,
                            'Quantity' => $data['Quantity 3']? $data['Quantity 3'] : 0,
                            'OnRequest' => 0,
                            'Valuta' => 'EURO'
                        ],
                        [
                            'Price' => $data['VK 4']? $data['VK 4'] : 0,
                            'Quantity' => $data['Quantity 4']? $data['Quantity 4'] : 0,
                            'OnRequest' => 0,
                            'Valuta' => 'EURO'
                        ],
                        [
                            'Price' => $data['VK 5']? $data['VK 5'] : 0,
                            'Quantity' => $data['Quantity 5']? $data['Quantity 5'] : 0,
                            'OnRequest' => 0,
                            'Valuta' => 'EURO'
                        ],
                        [
                            'Price' => $data['VK 6']? $data['VK 6'] : 0,
                            'Quantity' => $data['Quantity 6']? $data['Quantity 6'] : 0,
                            'OnRequest' => 0,
                            'Valuta' => 'EURO'
                        ],
                        [
                            'Price' => $data['VK 7']? $data['VK 7'] : 0,
                            'Quantity' => $data['Quantity 7']? $data['Quantity 7'] : 0,
                            'OnRequest' => 0,
                            'Valuta' => 'EURO'
                        ],
                        [
                            'Price' => $data['VK 8']? $data['VK 8'] : 0,
                            'Quantity' => $data['Quantity 8']? $data['Quantity 8'] : 0,
                            'OnRequest' => 0,
                            'Valuta' => 'EURO'
                        ],
                    ],
                    'GeneralBuyingPrice' => [
                        [
                            'Price' => 0,
                            'Quantity' => 0,
                            'OnRequest' => 0,
                            'Valuta' => 'EURO',
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
                    'MinimumOrderQuantity' => $data['Minimum quantity'],
                    'VatSettingId' => 0
                ]
            ]
        ];

        $printGroups = explode(',', (string)$data['Print group (without deposit)']);
        $imprintText = [];

        foreach($printGroups as $groupId){
            $currentRow = $printerData[$groupId];

            $imprintText['de'] = [
                'Images' => [
                    [
                        'Url' => $data['Print level sketch JPG'],
                        'Description' => '',
                        'FileName' => '',
                    ]
                ],
                'Name' => $currentRow['Advertising name DE'],
                'Description' => $currentRow['Advertising display description DE'],
            ];

            $imprintText['en'] = [
                'Images' => [
                    [
                        'Url' => $data['Print level sketch JPG'],
                        'Description' => '',
                        'FileName' => '',
                    ]
                ],
                'Name' => $currentRow['Advertising name EN'],
                'Description' => $currentRow['Advertising display description EN'],
            ];

            $imprintText['fr'] = [
                'Images' => [
                    [
                        'Url' => $data['Print level sketch JPG'],
                        'Description' => '',
                        'FileName' => '',
                    ]
                ],
                'Name' => $currentRow['Advertising name FR'],
                'Description' => $currentRow['Advertising display description FR'],
            ];

            $imprintText['nl'] = [
                'Images' => [
                    [
                        'Url' => $data['Print level sketch JPG'],
                        'Description' => '',
                        'FileName' => '',
                    ]
                ],
                'Name' => $currentRow['Advertising name NL'],
                'Description' => $currentRow['Advertising display description NL'],
            ];

            $imprintText['it'] = [
                'Images' => [
                    [
                        'Url' => $data['Print level sketch JPG'],
                        'Description' => '',
                        'FileName' => '',
                    ]
                ],
                'Name' => $currentRow['Advertising name IT'],
                'Description' => $currentRow['Advertising display description IT'],
            ];

            $mainTable['ImprintPositions'] = [
                [
                    'PositionCode' => $currentRow['Advertising name DE'],
                    'UnstructuredInformation' => [
                        'MaxPrintArea' => $data['printing area']
                    ],
                    'ImprintLocationTexts' => $imprintText,
                    'ImprintOptions' => [
                        [
                            'ChildImprints' => [],
                            'PrintColorAsText' => [],
                            'Dimension' => $data['printing area'],
                            'ImprintTexts' => [
                                'de' => [
                                    'Name' => $currentRow['Print type DE'],
                                    'Description' => '',
                                    'Ink Type' => $currentRow['Printing inks DE'],
                                ],
                                'en' => [
                                    'Name' => $currentRow['Print type EN'],
                                    'Description' => '',
                                    'Ink Type' => $currentRow['Printing colors EN'],
                                ],
                                'fr' => [
                                    'Name' => $currentRow['Print type FR'],
                                    'Description' => '',
                                    'Ink Type' => $currentRow['Printing inks FR'],
                                ],
                                'nl' => [
                                    'Name' => $currentRow['Print type NL'],
                                    'Description' => '',
                                    'Ink Type' => $currentRow['Printing inks NL'],
                                ],
                                'it' => [
                                    'Name' => $currentRow['Print type IT'],
                                    'Description' => '',
                                    'Ink Type' => $currentRow['Printing inks IT'],
                                ],
                            ],
                            'Sku' => '',
                            'SupplierSku' => '',
                            'DimensionsHeight' => 0,
                            'DimensionsDiameter' => 0,
                            'DimensionsWidth' => 0,
                            'DimensionsDepth' => 0,
                            'ImprintType' => '',
                            'UnstructuredInformation' => [],
                            'PrintColor' => 0,
                            'IsActiveRegionBased' => [],
                            'IsActiveCountryBased' => [],
                            'ImportantInformation' => [],
                            'ProductPriceRegionBased' => [],
                            'ProductPriceCountryBased' => [
                                'DEU' => [
                                    'RecommendedSellingPrice' => [
                                        [
                                            'Price' => $data['I 1'],
                                            'Quantity' => $data['Amount: 1'],
                                            'OnRequest' => 0,
                                            'Valuta' => 'EURO',
                                        ],
                                        [
                                            'Price' => $data['l 2']? $data['l 2'] : 0,
                                            'Quantity' => $data['Quantity 2']? $data['Quantity 2'] : 0,
                                            'OnRequest' => 0,
                                            'Valuta' => 'EURO'
                                        ],
                                        [
                                            'Price' => $data['I 3']? $data['I 3'] : 0,
                                            'Quantity' => $data['Quantity 3']? $data['Quantity 3'] : 0,
                                            'OnRequest' => 0,
                                            'Valuta' => 'EURO'
                                        ],
                                        [
                                            'Price' => $data['I 4']? $data['I 4'] : 0,
                                            'Quantity' => $data['Quantity 4']? $data['Quantity 4'] : 0,
                                            'OnRequest' => 0,
                                            'Valuta' => 'EURO'
                                        ],
                                        [
                                            'Price' => $data['I 5']? $data['I 5'] : 0,
                                            'Quantity' => $data['Quantity 5']? $data['Quantity 5'] : 0,
                                            'OnRequest' => 0,
                                            'Valuta' => 'EURO'
                                        ],
                                        [
                                            'Price' => $data['I 6']? $data['I 6'] : 0,
                                            'Quantity' => $data['Quantity 6']? $data['Quantity 6'] : 0,
                                            'OnRequest' => 0,
                                            'Valuta' => 'EURO'
                                        ],
                                        [
                                            'Price' => $data['I 7']? $data['I 7'] : 0,
                                            'Quantity' => $data['Quantity 7']? $data['Quantity 7'] : 0,
                                            'OnRequest' => 0,
                                            'Valuta' => 'EURO'
                                        ],
                                        [
                                            'Price' => $data['I 8']? $data['I 8'] : 0,
                                            'Quantity' => $data['Quantity 8']? $data['Quantity 8'] : 0,
                                            'OnRequest' => 0,
                                            'Valuta' => 'EURO'
                                        ],
                                    ],
                                    'GeneralBuyingPrice' => [
                                        [
                                            'Price' => 0,
                                            'Quantity' => 0,
                                            'OnRequest' => 0,
                                            'Valuta' => 'EURO',
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
                                    'MinimumOrderQuantity' => $data['Minimum quantity'],
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
