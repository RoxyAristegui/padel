<?php 
namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Illuminate\Http\Request;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();
        $rute=$user->currentTeam!=null ? 'dashboard' : 'no-team';
        
        return $request->wantsJson()
                    ? new JsonResponse('', 201)
                    :redirect()->route($rute);
    }
}

?>
