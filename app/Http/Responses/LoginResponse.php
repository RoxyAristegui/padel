<?php 
namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\Request;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();
        if($user->currentTeam==null && $user->teams->count() >=1)
        {
            $user->switchTeam($user->teams->first());
        }
    
        $rute=$user->currentTeam!=null ? 'dashboard' : 'no-team';
        
        return $request->wantsJson()
                ? response()->json(['two_factor' => false])
                    :redirect()->route($rute);
    }
}

?>
