<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartidoDisponible extends Model
{
    protected $table = 'partido_disponibles';
    protected $fillable = ['partido_id', 'user_id', 'disponible'];

    public function partido() {
        return $this->belongsTo(Partido::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function disponibilidades() {
        return $this->hasMany(\App\Models\PartidoDisponible::class);
    }
}
