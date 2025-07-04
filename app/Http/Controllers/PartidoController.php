<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Jetstream\Jetstream;
use Inertia\Inertia;
use App\Models\PartidoDisponible;
use Illuminate\Support\Facades\DB;
use App\Models\PartidoConvocada;
use App\Models\User;
use App\Models\Team;

class PartidoController extends Controller
{

    public function list(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam;
        $partidos = \App\Models\Partido::with('convocadas')
        ->where('team_id', $team->id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($partido) {
            $array = $partido->toArray();
            $array['tiene_convocados'] = $partido->convocadas && $partido->convocadas->count() > 0;
            return $array;
        });
        $isAdmin = $user->isAdmin($team);

        return \Inertia\Inertia::render('Partidos/List', [
            'partidos' => $partidos->toArray(),
            'isAdmin' => $isAdmin,
        ]);
    }


    /**
     * Show the partido management screen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $teamId
     * @return \Inertia\Response
     */


    public function show(Request $request, $partidoId)
    {
        $partido=Partido::find($partidoId);

        Gate::authorize('view', $partido);
        $user = $request->user();
        $isAdmin = $user->isAdmin($partido->team);

        $completado = $partido->completado ?? false;
        $miDisponibilidad = PartidoDisponible::where('partido_id', $partidoId)
        ->where('user_id', $user->id)
        ->first();

        $disponibilidades = [];
        if ($isAdmin) {
            $disponibilidades = $partido->disponibilidades()->with('user')->get();
        }

        $convocados = $partido->convocadas()->with('user')->get();
        $miembrosEquipo = $partido->team->users;
        return Inertia::render('Partidos/Show', [
            'partido' => $partido,
            'isAdmin' => $isAdmin,
            'completado' => $completado,
            'miDisponibilidad' => $miDisponibilidad,
            'disponibilidades' => $disponibilidades,
            'convocados' => $convocados,
            'miembrosEquipo' => $miembrosEquipo,
        ]);
    }

    /**
     * Show the team creation screen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function create(Request $request)
    {
        Gate::authorize('create', Partido::class);

        return Inertia::render('Partidos/Create');
    }

    /**
     * Create a new team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        
        $team=$request->user()->currentTeam;
        // $partidos= Partido::where('team_id',$team->id)->get();
        $jornada= $team->partidos->isNotEmpty()? ($team->partidos->last()->jornada)+1 : 1; 
        $data=$request->all();
        
        $formatted_data=[
            'horario'=>Carbon::parse($data['horario'])->setTimezone('Europe/Madrid')->toDateTimeString(),
            'club'=>$data['club'],
            'rival'=>$data['rival'],
            'jornada'=>$jornada
        ];
        
        $partido=$team->partidos()->create($formatted_data);
        return redirect()->route('partido.show', ['partido' => $partido->id]);
    }



    /**
     * Delete the given partido.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $teamId
     * @return \Illuminate\Http\RedirectResponse
     */
  public function destroy(Request $request, $partidoId)
    {
        $user = $request->user();
        $partido = \App\Models\Partido::with('disponibilidades', 'team')->findOrFail($partidoId);
    
        // Solo admin puede eliminar
        if (!$user->isAdmin($partido->team)) {
            abort(403, 'No autorizado');
        }
    
        // Solo si no hay usuarios disponibles
        if ($partido->disponibilidades()->where('disponible', true)->count() > 0) {
            return back()->withErrors(['msg' => 'No se puede eliminar un partido con usuarios disponibles.']);
        }
    
        $partido->delete();
    
        return redirect()->route('partidos.list');
    }



public function marcarDisponibilidad(Request $request, $partidoId)
{
    $user = $request->user();
    $partido = Partido::findOrFail($partidoId);

    // No permitir si el partido está completado
    if ($partido->completado) {
        return back()->withErrors(['msg' => 'El partido ya está cerrado.']);
    }

    PartidoDisponible::updateOrCreate(
        [
            'partido_id' => $partidoId,
            'user_id' => $user->id,
        ],
        [
            'disponible' => $request->input('disponible') ? 1 : 0,
        ]
    );

    return back();
}

public function completar(Request $request, $partidoId)
{
    $user = $request->user();
    $partido = Partido::findOrFail($partidoId);

    if (!$user->isAdmin($partido->team)) {
        abort(403, 'No autorizado');
    }

    $partido->completado = true;
    $partido->save();

    return back();
}

public function convocar(Request $request, $partidoId)
{
    $user = $request->user();
    $partido = Partido::with('team')->findOrFail($partidoId);

    if (!$user->isAdmin($partido->team) || !$partido->completado) {
        abort(403, 'No autorizado');
    }

    // Borra convocados anteriores
    PartidoConvocada::where('partido_id', $partidoId)->delete();

    // Guarda los nuevos
    $convocados = $request->input('convocados', []);
    foreach ($convocados as $userId) {
        PartidoConvocada::create([
            'partido_id' => $partidoId,
            'user_id' => $userId,
        ]);
    }

    return back();
}

public function update(Request $request, $partidoId)
{
    $user = $request->user();
    $partido = \App\Models\Partido::findOrFail($partidoId);
    if (!$user->isAdmin($partido->team)) {
        abort(403, 'No autorizado');
    }
    $data = $request->validate([
        'horario' => 'required',
    ]);
    
    $nuevoHorario = Carbon::parse($data['horario'])->setTimezone('Europe/Madrid');
    $editado = $partido->horario != $nuevoHorario;

    $partido->horario = $nuevoHorario;
    if ($editado) {
        $partido->editado = true;
    }
    $partido->save();
    return back();
}

    public function estadisticas(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam;
        $miembros = $team->users;
        $partidos = $team->partidos;
        $jugadores = [];
        $totalPartidos = $partidos->count();
        foreach ($miembros as $jugador) {
            $jugadores[] = $jugador->estadisticas($team);
        }
     
        return Inertia::render('Estadisticas', [
            'partidos_disponutados'=> $totalPartidos,
            'jugadores' => $jugadores,
        ]);
    }

    public function disponiblesConEstadisticas(Request $request, $partidoId)
    {
        $user = $request->user();
        $partido = \App\Models\Partido::with('team')->findOrFail($partidoId);
        $team = $partido->team;
        $miembros = $team->users;
        if (!$miembros->contains('id', $team->owner_id)) {
            $miembros->push($team->owner);
        }
        $disponibles = [];
        $noDisponibles = [];
        foreach ($miembros as $jugador) {
            $estadisticas = method_exists($jugador, 'estadisticas') ? $jugador->estadisticas($team) : null;
            $disponibilidad = $partido->disponibilidades->firstWhere('user_id', $jugador->id);
            $item = [
                'user' => [
                    'id' => $jugador->id,
                    'name' => $jugador->name,
                    'estadisticas' => $estadisticas,
                ],
                'disponible' => $disponibilidad ? (bool)$disponibilidad->disponible : false,
            ];
            if ($item['disponible']) {
                $disponibles[] = $item;
            } else {
                $noDisponibles[] = $item;
            }
        }
        return response()->json([
            'disponibles' => $disponibles,
            'noDisponibles' => $noDisponibles,
        ]);
    }
}
