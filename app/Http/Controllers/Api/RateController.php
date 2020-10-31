<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rate;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class RateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator|LengthAwarePaginator
     */
    public function index()
    {
        $request = request();
        $rates=Rate::query();
        $rates->with(array('currency'=>function($query){
            $query->select('name','id','sign');
        }));
        if ($request->has('currencies')) {
            $currencies=$request->currencies;
            $currencies = explode(',', $currencies);
            $rates->whereHas('currency',function($query) use($currencies){
                $query->whereIn('id',$currencies);
            });
        }
        return $request->has('perPage') ? $rates->paginate($request->perPage) : $rates->paginate();
    }

    /**
     * @param Rate $rate
     * @param Request $request
     * @return mixed
     */
    public function update(Rate $rate, Request $request){
        $data=$request->validate([
            'since' => ['required', 'date'],
            'amount' => ['required', 'numeric'],
            'message' => ['nullable','string'],
            'currency_id' => ['required', 'exists:currencies,id'],
        ]);
        return $rate->update($data);
    }

    /**
     * @param Rate $rate
     * @return Response
     * @throws Exception
     */
    public function destroy(Rate $rate)
    {
        $rate->delete();
        return response('rate deleted',Response::HTTP_NO_CONTENT);
    }

    /**
     * @param $currencyId
     * @return Collection
     */
    public function get($currencyId)
    {
        return Rate::whereCurrencyId($currencyId)
            ->where('since','<=',Carbon::now())
            ->orderBy('since', 'DESC')->first();
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
                'currency_id' => 'required',
                'since'    => 'required|date',
                'amount'   => 'required|Numeric',
                'message'   => 'string',
            ]);
            return Rate::create([
                'currency_id' => $validated['currency_id'],
                'amount'      => $validated['amount'],
                'since'       => Carbon::parse($validated['since']),
                'message'       => $validated['message'],
            ]);
        }
    //
}
