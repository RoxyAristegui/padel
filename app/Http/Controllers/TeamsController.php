<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MembersImport;
use App\Models\Team;    
use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\InviteTeamMember;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Contracts\InvitesTeamMembers;
use Laravel\Jetstream\Events\InvitingTeamMember;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Mail\TeamInvitation;
use Laravel\Jetstream\Rules\Role;   
class TeamsController extends Controller
{
    public function addUsers(Request $request,$team_id){
        if (!$request->hasFile('file')) {
            return redirect()->back()->withErrors(['file' => 'No se ha subido ningún archivo.','excelUpload' => 'No se ha subido ningún archivo.']);
        }

        $collection = Excel::toCollection(new MembersImport, $request->file('file'));
        $emails = $collection->flatten()->filter(function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });

        if ($emails->isEmpty()) {
            return redirect()->back()->withErrors(['file' => 'No se encontraron emails válidos en el archivo.', 'ExcelUpload']);
        }

        $user = $request->user();
        $team = Team::findOrFail($team_id);
        $role="member";
         $errors=collect();
         $emails_procesados=collect();
        foreach($emails as $email){
            Gate::forUser($user)->authorize('addTeamMember', $team);

             $validator= Validator::make([
                'email' => $email,
                'role' => $role,
                'file' => $request->file('file'),
            ],  array_filter([
                'file' => 'required',
                'email' => [
                    'required', 'email',
                    Rule::unique(Jetstream::teamInvitationModel())->where(function (Builder $query) use ($team) {
                        $query->where('team_id', $team->id);
                    }),
                ],
                'role' => Jetstream::hasRoles()
                                ? ['required', 'string', new Role]
                                : null,
            ]), [
                'email.unique' => __('This user has already been invited to the team.'.$email),
            ])->after(function ($validator) use ($team, $email) {
                $validator->errors()->addIf(
                    $team->hasUserWithEmail($email),
                    'email',
                    __('This user already belongs to the team.'.$email)
                );
            });
            
            if($validator->fails()){
                if($errors->IsEmpty()){
                    $errors=$validator->errors();
                }else{
                    $errors->merge($validator->errors());
                }
            }else{
                
                InvitingTeamMember::dispatch($team, $email, $role);
        
                $invitation = $team->teamInvitations()->create([
                    'email' => $email,
                    'role' => $role,
                ]);
        
                Mail::to($email)->send(new TeamInvitation($invitation));
                $inviteTeamMember=new InviteTeamMember();
                $inviteTeamMember->invite(
                    $request->user(),
                    $team,
                    $email,
                    'member'
            );  
            }
            $emails_procesados->push($email);
        }
        // dd($emails_procesados);
        if($errors->isNotEmpty()){
        
            return back()->withErrors($errors);
        }
        // $emails_importados = $emails->count();
        return redirect()->back()->with('success', "Importados  $emails_procesados usuarios correctamente");
    }
}
