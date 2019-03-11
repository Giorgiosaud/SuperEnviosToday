<?php

    namespace App\Http\Controllers;

    use App\Currency;
    use App\Rate;
    use Carbon\Carbon;
    use Illuminate\Http\Request;

    /**
     * Class RateController
     * @package App\Http\Controllers
     */
    class RateController extends Controller
    {
        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
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
         * @return mixed
         */
        public function lastRate()
        {
            return Rate::orderBy('since', 'DESC')->first();
        }


        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function create()
        {
            //
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         *
         * @return \Illuminate\Http\Response
         */
        public function store(Request $request)
        {
            $validated = $request->validate([
                'currency' => 'required',
                'since' => 'required|date',
                'amount' => 'required|Numeric',
            ]);
            return Rate::create([
                'currency_id' => $validated['currency']['id'],
                'amount' => $validated['amount'],
                'since' => Carbon::parse($validated['since']),
            ]);
        }

        /**
         * Display the specified resource.
         *
         * @param \App\Rate $rate
         *
         * @return \Illuminate\Http\Response
         */
        public function show(Rate $rate)
        {
            //
        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param \App\Rate $rate
         *
         * @return \Illuminate\Http\Response
         */
        public function edit(Rate $rate)
        {
            //
        }

        /**
         * Update the specified resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @param \App\Rate $rate
         *
         * @return Rate
         */
        public function update(Request $request, Rate $rate)
        {
            $validated = $request->validate([
                'currency' => 'required',
                'since' => 'required|date',
                'amount' => 'required|Numeric',
            ]);
            if ($rate->update([
                'currency_id' => $validated['currency']['id'],
                'amount' => $validated['amount'],
                'since' => Carbon::parse($validated['since']),
            ]))
                return $rate;
            return response()->json(['status' => 500, 'message' => 'Successfully Edited']);;
        }

        /**
         * @param Rate $rate
         * @return bool|null
         * @throws \Exception
         */
        public
        function destroy(Rate $rate)
        {
            if ($rate->delete())
                return response()->json(['message' => 'Successfully Deleted']);;
        }
    }
