<?php

namespace App\Exports;

use App\Models\ProductsModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        return ProductsModel::all();
    }
    public function headings(): array
    {
        return [
            'product_id',
            'product_name',
            'category_id',
            'supplier_id',
            'barcode',
            'barcode_image',
            'quantity',
            'price_in',
            'price_out',
            'in_stock',
            'image',
            'created_at',
            'updated_at',
        ];
    }
}
