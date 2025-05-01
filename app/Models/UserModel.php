<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable; //implementasi class authenticatable

class UserModel extends Authenticatable implements JWTSubject
{

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    use HasFactory;

    protected $table = 'm_user';  // Mendefinisikan nama tabel yang digunakan oleh model ini
    protected $primaryKey = 'user_id';  // Mendefinisikan primary key dari tabel yang digunakan

    protected $fillable = [
        'level_id',
        'username',
        'nama',
        'password',
        'image',
        'created_at',
        'updated_at'
    ];

    public function level()
    {
        return $this->belongsTo(LevelModel::class, 'level_id', 'level_id');

    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => url('/storage/posts/'. $image),
        );
    }
}
