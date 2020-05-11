<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $request = request();
        $users = User::with('roles');
        $filters = ['name', 'last_name', 'idn', 'idn_type', 'email', 'address', 'phone', 'email_verified_at'];
        foreach ($filters as $filter) {
            if ($request->has($filter)) {
                $users->where($filter, 'like', '%' . $request[$filter] . '%');
            }
        }
        if ($request->has('roles')) {
            $roles=$request->roles;
            $users->whereHas('roles',function($query) use($roles){
                $query->whereIn('name_id',$roles);
            });
        }
        $perPage = $request->has('perPage') ? $request->get('perPage') : config('app.paginated_by');

        return $users->paginate($perPage);
    }

    /**
     * @param $idnType
     * @param $idn
     * @return ResponseFactory|Response
     */
    public function search($idnType, $idn){
        $users= User::select('id','idn','idn_type','name','last_name','email','phone')->where('idn_type',$idnType)->where('idn',$idn)->get();

        if($users->count()==0){
            return $this->checkIfUserIdMismatchOrNotFound($idn, $users);
        }
        return ['status'=>'OK','user'=>$users->first()];
    }

    /**
     * @param $idn
     * @return ResponseFactory|Response
     */
    private function checkIfUserIdMismatchOrNotFound($idn)
    {
        $user = User::select('id', 'idn','idn_type','name', 'last_name', 'email', 'phone')->where('idn', 'LIKE','%'.$idn.'%')->get();
        if ($user->count() == 0) {
            return response('No user Found', 204);
        }

        return response(['user'=>$user->first()], 200);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request){
        $data=$request->validate([
            'idn_type' => ['required', 'in:CI,PASSPORT,RUT,DNI,RIF'],
            'idn' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required','string','max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['max:255'],
        ]);
        $data['password']=bcrypt('cliente');
        event(new Registered($user = User::create($data)));
        return ['status'=>'OK','user'=>$user];

    }
    public function resendVerificationEmail(Request $request){
        $data=$request->validate([
            'id'=>['required','exists:App\User,id']
        ]);
        $user=User::find($data['id']);
        $user->sendEmailVerificationNotification();

        return $request->wantsJson()
            ? new Response('', 202)
            : back()->with('resent', true);
    }

}
