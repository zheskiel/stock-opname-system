<?php
namespace App\Repositories;

use App\Models\Master;

class MasterDataRepository extends BaseRepository
{
    protected $master;

    public function __construct(Master $master)
    {
        $this->master = $master;
    }

    public function save($data)
    {
        $params = [
            'product_id'        => $data["Product ID"],
            'category'          => $data["Category"],
            'subcategory'       => $data["Subcategory"],
            'category_type'     => $data["Category Type"],
            'bom_name'          => $data["BOM Name"],
            'product_code'      => $data["Product Code"],
            'product_name'      => $data["Product Name"],
            'base_price'        => $data["Base Price"],
            'requestable'       => $data["Requestable"],
            'receipt_tolerance' => $data["Receipt Tolerance (%)"],
            'saleable'          => $data["Saleable(YES/NO)"],
            'notes'             => $data["Notes"],
            'vat'               => $data["VAT"],
            'status_uom'        => $data["Status UOM"],
            'formula'           => $data["Formula Of These Menu"],
            'units'             => $data['units'],
            'owned'             => $data['owned']
        ];

        $this->master->firstOrCreate([
            'product_id' => $data["Product ID"]
        ], $params);
    }
}