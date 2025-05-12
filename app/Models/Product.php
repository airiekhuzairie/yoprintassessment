<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'UNIQUE_KEY', 'PRODUCT_TITLE', 'PRODUCT_DESCRIPTION', 'STYLE#',
        'SANMAR_MAINFRAME_COLOR', 'SIZE', 'COLOR_NAME', 'PIECE_PRICE'
    ];
}