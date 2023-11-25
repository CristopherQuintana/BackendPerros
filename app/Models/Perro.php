<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perro extends Model
{
    protected $table = 'perros';
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['nombre', 'foto_url', 'descripcion']; // Asegúrate de tener los campos adecuados aquí
    protected $dates = ['deleted_at'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}