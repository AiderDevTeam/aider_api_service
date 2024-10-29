<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = ['external_id', 'name', 'disk', 'size', 'url', 'mime', 'path'];

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }
}
