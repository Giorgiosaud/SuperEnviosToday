<?php

  namespace App\Http\Controllers\Api;

  use App\Events\CreatedBank;
  use App\Events\RestoredBank;
  use App\Http\Controllers\Controller;
  use App\Models\Bank;
  use App\Models\Currency;
  use Exception;
  use Illuminate\Contracts\Pagination\LengthAwarePaginator;
  use Illuminate\Database\Eloquent\Collection;
  use Illuminate\Http\Request;
  use Symfony\Component\HttpFoundation\Response;

  class BankController extends Controller
  {
    /**
     */
    public function index()
    {
      $request = request();
      $banks = Bank::query();
      $banks->with(array('currency' => function ($query) {
        $query->select('name', 'id');
      }));
      if ($request->has('name')) {
        $banks->where('name', 'like', '%' . $request->name . '%');
      }
      if ($request->has('currencies')) {
        $currencies = $request->currencies;
        $currencies = explode(',', $currencies);
        $banks->whereHas('currency', function ($query) use ($currencies) {
          $query->whereIn('id', $currencies);
        });
      }
      return $request->has('perPage') ? $banks->paginate($request->perPage) : $banks->paginate();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function update(Bank $bank, Request $request)
    {
      $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
      ]);
      return $bank->update($data);

    }

    /**
     * @return Collection
     */
    public function baseBanks()
    {
      $currency = Currency::find(config('app.base_currency_id'));
      return $currency->banks;
    }

    /**
     * @param Bank $bank
     * @return Response
     * @throws Exception
     */
    public function destroy(Bank $bank)
    {
      $bank->delete();
      if ($bank->accounts->count() == 0) {
        $bank->forceDelete();
      }
      return response('bank deleted', Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response $response
     */
    public function store(Request $request)
    {
      $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'currency' => ['required', 'numeric', 'exists:currencies,id'],
      ]);
      $data['currency_id'] = $data['currency'];
      $bank = Bank::where('name', $data['name'])
        ->where('currency_id', $data['currency'])
        ->withTrashed()
        ->first();
      if (!$bank) {
        $bank = Bank::create($data);
        event(new CreatedBank($bank));
      } else {
        $bank->restore();
        event(new RestoredBank($bank));
      }
      return response('bank created', Response::HTTP_CREATED);
    }
  }
