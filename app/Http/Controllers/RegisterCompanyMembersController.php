<?php

    namespace App\Http\Controllers;

    use App\Events\RegisteredOperator;
    use App\User;
    use Illuminate\Auth\Events\Registered;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;

    /**
     * Class RegisterCompanyMembersController
     * @package App\Http\Controllers
     */
    class RegisterCompanyMembersController extends Controller
    {
        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function create()
        {
            return view('auth.registerMembers');
        }

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function createAndAssignAccount()
        {
            return view('auth.assignAccounts');

        }

        /**
         * @param Request $request
         * @return mixed
         */
        public function save(Request $request)
        {
            $validated = $request->validate([
                'email' => 'required|email|confirmed',
                'address' => 'required|string',
                'phone' => 'required|string|min:9',
                'idn' => 'required',
                'idn_type' => 'required|in:PASSPORT,RUT,CI,DNI',
                'name' => 'required',
                'password' => 'required|string|min:6|confirmed|',
                'last_name' => 'required',
                'roles' => 'required',
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $user = User::create($validated);
            foreach ($validated['roles'] as $role) {
                $user->setRole($role);
            }
            event(new RegisteredOperator($user));
            return $user;

        }

        /**
         * @param Request $request
         * @return mixed
         */
        public function saveClient(Request $request)
        {
            $validated = $request->validate([
                'email' => 'required|email|confirmed',
                'address' => 'required|string',
                'phone' => 'required|string|min:9',
                'idn' => 'required',
                'idn_type' => 'required|in:PASSPORT,RUT,CI,DNI',
                'name' => 'required',
                'last_name' => 'required',
                'relatedSender' => 'numeric'
            ]);

            $validated['password'] = Hash::make('secret');
            $user = User::whereIdn($request->only(['idn']))->whereIdnType($request->only(['idn_type']))->first();
            if (!$user) {
                $user = new User($validated);
            }
            $user->save();
            if (isset($validated['relatedSender'])) {
                $user->senders()->attach($validated['relatedSender']);
            }
            event(new Registered($user));
            $user->setRole('receiver');
            return $user;

        }

    }

