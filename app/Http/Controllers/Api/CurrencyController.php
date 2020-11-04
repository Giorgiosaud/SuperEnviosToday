<?php

namespace App\Http\Controllers\Api;

use App\Events\CreatedCurrency;
use App\Events\RestoredCurrency;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CurrencyController extends Controller
{
    public function __construct(){
      $this->middleware('role:coordinator', ['only' => ['store', 'destroy', 'update']]);
    }
    public function indexList(){
        return Currency::select('id','name','identifier')->all();
    }

    /**
     * @return mixed
     */
    public function foreign(){

        return Currency::where('id','!=',config('app.base_currency_id'))->get();
    }
    public function index()
    {
        $request = request();
        $currency=Currency::query();
        if ($request->has('name')) {
            $currency->where('name', 'like', '%' . $request->name . '%');
        }

        return $request->has('perPage') ? $currency->paginate($request->perPage) : $currency->paginate();
    }
    /**
     * @param Request $request
     * @return mixed
     */
    public function update(Currency $currency, Request $request){
        $data=$request->validate([
            'name' => ['required', 'string', 'max:255'],
            'identifier' => ['required', 'string', 'max:255'],
            'sign' => ['required', 'string', 'max:255'],
        ]);
        return $currency->update($data);

    }

    /**
     * @param Currency $currency
     * @return Response
     * @throws Exception
     */
    public function destroy(Currency $currency)
    {
        $currency->delete();
        if($currency->banks->count()==0){
            $currency->forceDelete();
        }
        return response('currency deleted',Response::HTTP_NO_CONTENT);
    }
    /**
     * @param Request $request
     * @return Response $response
     */
    public function store(Request $request){
        $data=$request->validate([
            'name' => ['required', 'string', 'max:255'],
            'identifier' => ['required', 'string', 'max:255'],
            'sign' => ['required', 'string', 'max:255'],
        ]);
        $currency=Currency::where('name',$data['name'])
            ->where('identifier',$data['identifier'])
            ->where('sign',$data['sign'])
            ->withTrashed()
            ->first();
        if(!$currency){
            $currency = Currency::create($data);
            event(new CreatedCurrency($currency));
        }else{
            $currency->restore();
            event(new RestoredCurrency($currency));
        }
        return response('currency created',Response::HTTP_CREATED);
    }
    //
}
