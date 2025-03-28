<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    protected $fillable = ['url', 'senha', 'user_id', 'name'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
