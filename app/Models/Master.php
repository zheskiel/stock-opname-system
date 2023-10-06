<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Master extends Model
{
    protected $table = "master";

    protected $fillable = [
        'product_id',
        'category',
        'subcategory',
        'category_type',
        'bom_name',
        'product_code',
        'product_name',
        'base_price',
        'requestable',
        'receipt_tolerance',
        'saleable',
        'notes',
        'vat',
        'status_uom',
        'formula',
        'owned',
        'units',
    ];

    protected $hidden = [
        // 'id',
        'created_at',
        'updated_at'
    ];

    protected $casts = [];

    public function fetchAll()
    {
        return $this
            ->get()
            ->map(function($query) {
                $units = json_decode($query->units, true);

                uasort($units, function ($item1, $item2) {
                    return $item2['value'] <=> $item1['value'];
                });

                $query->units = $units;

                return $query;
            });
    }
}
