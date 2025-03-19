<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelModel extends Model
{
    use HasFactory;

    protected $table = 'm_level'; // Nama tabel

    protected $primaryKey = 'level_id'; // Fix primary key kamu di sini!

    public $timestamps = true; // Kalau nggak ada created_at dan updated_at di tabel, set ke false

    protected $fillable = [
        'level_kode',
        'level_nama'
    ];
}
