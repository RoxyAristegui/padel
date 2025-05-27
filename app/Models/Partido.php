<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Jetstream\Jetstream;
use App\Models\PartidoConvocada;

class Partido extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'jornada',
        'horario',
        'club',
        'rival',
        'editado',
    ];

    /**
     * Get the team that the partido belongs to.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Jetstream::teamModel());
    }

    public function disponibilidades()
    {
        return $this->hasMany(\App\Models\PartidoDisponible::class);
    }
    public function convocadas()
{
    return $this->hasMany(PartidoConvocada::class);
}
}
