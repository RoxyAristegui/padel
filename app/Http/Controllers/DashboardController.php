<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partido;
use App\Models\PartidoDisponible;
use App\Models\PartidoConvocada;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam;
        $partido = Partido::with(['team', 'convocadas', 'disponibilidades'])
            ->where('team_id', $team->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $miDisponibilidad = null;
        $tieneConvocados = false;
        $convocadosCount = 0;
        $partidoData = null;
        if ($partido) {
            $miDisponibilidad = PartidoDisponible::where('partido_id', $partido->id)
                ->where('user_id', $user->id)
                ->first();
            $tieneConvocados = $partido->convocadas && $partido->convocadas->count() > 0;
            $convocadosCount = $partido->convocadas ? $partido->convocadas->count() : 0;
          
            $fecha = Carbon::parse($partido->horario)->isoFormat('D [de] MMMM, YYYY');
            $hora = Carbon::parse($partido->horario)->format('H:i');
            $partidoData = [
                'id' => $partido->id,
                'fecha' => $fecha,
                'hora' => $hora,
                'club' => $partido->club,
                'rival' => $partido->rival ,
                'editado' => $partido->editado,
                'completado' => $partido->completado,
                'convocadas' => $partido->convocadas,
                'disponibilidades' => $partido->disponibilidades,
            ];
        }

        return Inertia::render('Dashboard', [
            'partido' => $partidoData,
            'miDisponibilidad' => $miDisponibilidad,
            'tieneConvocados' => $tieneConvocados,
            'convocadosCount' => $convocadosCount,
        ]);
    }
}
