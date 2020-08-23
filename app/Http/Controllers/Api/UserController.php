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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index()
    {
      $request = request();
      $users = User::query();
      $users->with(array('roles' => function ($query) {
        $query->select('name', 'name_id');
      }));
      $filters = ['name', 'last_name', 'idn', 'idn_type', 'email', 'address', 'phone', 'email_verified_at'];
      foreach ($filters as $filter) {
        if ($request->has($filter)) {
          $users->where($filter, 'like', '%' . $request[$filter] . '%');
        }
      }
      if ($request->has('roles')) {
        $roles = $request->roles;
        $roles = explode(',', $roles);
        $users->whereHas('roles', function ($query) use ($roles) {
          $query->whereIn('name_id', $roles);
        });
      }
      $perPage = $request->has('perPage') ? $request->get('perPage') : config('app.paginated_by');

      return $users->select('id', 'idn', 'idn_type', 'name', 'last_name', 'email', 'phone')
        ->paginate($perPage);
    }

    public function getOperators()
    {
      return User::whereHas('roles', function ($q) {
        $q->whereIn('role_name_id', ['foreign_operator', 'venezuelan_operator']);
      })
        ->with('roles')
        ->get();
    }

    /**
     * @param $idnType
     * @param $idn
     * @return array|ResponseFactory|Response
     */
    public function search($idnType, $idn)
    {
      $users = User::select('id', 'idn', 'idn_type', 'name', 'last_name', 'email', 'phone')->where('idn_type', $idnType)->where('idn', $idn)->get();

      if ($users->count() == 0) {
        return $this->checkIfUserIdMismatchOrNotFound($idn, $users);
      }
      return ['status' => 'OK', 'user' => $users->first()];
    }

    /**
     * @param $idn
     * @return ResponseFactory|Response
     */
    private function checkIfUserIdMismatchOrNotFound($idn)
    {
      $user = User::select('id', 'idn', 'idn_type', 'name', 'last_name', 'email', 'phone')->where('idn', 'LIKE', '%' . $idn . '%')->get();
      if ($user->count() == 0) {
        return response('No user Found', 204);
      }

      return response(['user' => $user->first()], 200);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
      $data = $request->validate([
        'idn_type' => ['required', 'in:CI,PASSPORT,RUT,DNI,RIF'],
        'idn' => ['required', 'string', 'max:20'],
        'name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255'],
        'phone' => ['max:255'],
      ]);
      $data['password'] = bcrypt('cliente');
      event(new Registered($user = User::create($data)));
      return ['status' => 'OK', 'user' => $user];

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|Response
     */
    public function resendVerificationEmail(Request $request)
    {
      $data = $request->validate([
        'id' => ['required', 'exists:App\User,id']
      ]);
      $user = User::find($data['id']);
      $user->sendEmailVerificationNotification();

      return $request->wantsJson()
        ? new Response('', 202)
        : back()->with('resent', true);
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function receivers(User $user)
    {
      return $user->receivers;
    }

    /**
     * @param User $user
     * @param Request $request
     * @return User
     */
    public function createReceiver(User $user, Request $request)
    {
      $data = $request->validate([
        'idn_type' => ['required', 'in:CI,PASSPORT,RUT,DNI,RIF'],
        'idn' => ['required', 'string', 'max:20'],
        'name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255'],
      ]);
      $data['password'] = bcrypt('receiver');
      $receiver = User::whereIdn($data['idn'])->withTrashed()->where('idn_type', $data['idn_type'])->first();
      if (!$receiver) {
        $receiver = User::create($data);
      } else {
        $receiver->update($data);
        $receiver->restore();
      }
      $receiversIds = $user->receivers->pluck('id')->toArray();
      array_push($receiversIds, $receiver->id);
      $user->receivers()->sync($receiversIds);
      return $user;
    }

    public function update(User $user, Request $request)
    {
      $data = $request->validate([
        'idn_type' => ['required', 'in:CI,PASSPORT,RUT,DNI,RIF'],
        'idn' => ['required', 'string', 'max:20'],
        'name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255'],
      ]);
      $user->update($data);
      return $user;
    }

    public function unlink(User $client, User $receiver)
    {
      $client->receivers()->detach($receiver->id);
      if ($receiver->senders->count() == 0) {
        $receiver->delete();
      }
      return
        response('transaction executed', 204);
    }

  }
