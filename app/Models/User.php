<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin($team)
    {
        return $this->hasTeamRole($team, 'Administrator');
    }

    public function estadisticas($team){
     $partidos = $team->partidos;
        $totalPartidos = $partidos->count();
            $disponibles = PartidoDisponible::where('user_id', $this->id)
                ->whereIn('partido_id', $partidos->pluck('id'))
                ->where('disponible', true)
                ->count();
            $convocados = PartidoConvocada::where('user_id', $this->id)
                ->whereIn('partido_id', $partidos->pluck('id'))
                ->count();
            $porcentaje_disponible = $totalPartidos > 0 ? round(($disponibles / $totalPartidos) * 100, 1) : 0;
            $porcentaje_convocado = $disponibles > 0 ? round(($convocados / $disponibles) * 100, 1) : 0;
            return [
                'id' => $this->id,
                'name' => $this->name,
                'porcentaje_disponible' => $porcentaje_disponible,
                'porcentaje_convocado' => $porcentaje_convocado,
            ];
    
    }

}
