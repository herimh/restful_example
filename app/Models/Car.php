<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = ['name', 'price', 'doors', 'km', 'year', 'transmission', 'image_url'];

}