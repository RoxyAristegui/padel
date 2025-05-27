<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartidoConvocada extends Model
{
    protected $table = 'partido_convocadas';
    protected $fillable = ['partido_id', 'user_id'];

    public function partido() {
        return $this->belongsTo(Partido::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }


}