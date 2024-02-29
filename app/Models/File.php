<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = ['file_path']; // Add 'file_path' here

    use HasFactory;
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}