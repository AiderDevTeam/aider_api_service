<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['external_id', 'title', 'message', 'type_external_id'];

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }
}
