<?php

namespace App\Http\Controllers;

use App\Rate;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class RateController.
 */
    class RateController extends Controller
    {
        /**
         * Display a listing of the resource.
         *
         * @return Response
         */
        public function index()
        {
            return view('auth.rate');
            //
        }

        /**
         * @return mixed
         */
        public function allRates(Request $request)
        {
            $limit = $request->has('perPage') ? $request->get('perPage') : 20;

            return Rate::paginate($limit);
        }

        /**
         * @param $id
         *
         * @return mixed
         */
        public function lastRate($id)
        {
            return Rate::whereCurrencyId($id)->orderBy('since', 'DESC')->first();
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param Request $request
         *
         * @return Rate|Model
         */
        public function store(Request $request)
        {
            $validated = $request->validate([
                'currency' => 'required',
                'since'    => 'required|date',
                'amount'   => 'required|Numeric',
            ]);

            return Rate::create([
                'currency_id' => $validated['currency']['id'],
                'amount'      => $validated['amount'],
                'since'       => Carbon::parse($validated['since']),
            ]);
        }

        /**
         * Update the specified resource in storage.
         *
         * @param Request $request
         * @param Rate    $rate
         *
         * @return mixed
         */
        public function update(Request $request, Rate $rate)
        {
            $validated = $request->validate([
                'currency' => 'required',
                'since'    => 'required|date',
                'amount'   => 'required|Numeric',
            ]);

            return $rate->update([
                'currency_id' => $validated['currency']['id'],
                'amount'      => $validated['amount'],
                'since'       => Carbon::parse($validated['since']),
            ]);
        }

        /**
         * @param Rate $rate
         *
         * @throws Exception
         *
         * @return mixed
         */
        public function destroy(Rate $rate)
        {
            return $rate->delete();
        }
    }
