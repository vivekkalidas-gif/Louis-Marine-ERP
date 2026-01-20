<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteRequestItem extends Model
{
    protected $fillable = ['quote_request_id', 'website_product_id', 'quantity'];

    public function quoteRequest()
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    public function websiteProduct()
    {
        return $this->belongsTo(WebsiteProduct::class, 'website_product_id');
    }
}
