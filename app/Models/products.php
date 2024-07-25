<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products extends Model
{
    public function section()
    {
        return $this->belongsTo(sections::class);
    }
    protected $table = "products";
    protected $fillable = [
        "Product_name",
        "description",
        "section_id",
    ];

    use HasFactory;
}
