<?php

namespace App\Imports;

use App\Models\ProductsModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Picqer;

class ProductsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    private $current = 0;

    public function GenBarcode(){
        $barcode_rand = rand(1000000,1999999);
        $result = ProductsModel::select('barcode')->get();

        if($result->contains('barcode', $barcode_rand)){
            $this->GenBarcode();
        }

        return $barcode_rand;

    }

    public function model(array $row)
    {
        $this->current++; 
        if($this->current > 1){
            $result = new ProductsModel();

            $result->Product_Name = $row[0];
            $result->Category_Id = $row[1];
            $result->Supplier_Id = $row[2];
            $result->Quantity = $row[3];
            $result->Price_In = $row[4];
            $result->Price_Out = $row[5];
            
            if($row[6]==1){
                $barcode_rand = $this->GenBarcode();  
                $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
                $barcodeimage = $generator->getBarcode($barcode_rand, $generator::TYPE_CODE_128);
    
                $result->Barcode = $barcode_rand;
                $result->barcode_image = $barcodeimage;
            }

            $result->in_stock = $row[7];
            $result->image = $row[8];
            $result->save();
        }
    }
}
