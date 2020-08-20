<?php
use Illuminate\Http\Request;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;
use Stripe\Subscription;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/plans', function (Request $request) {
    try {
        if (!$request->has("api_key")) {
            return response()->json([
                'status'  => 401,
                'message' => 'Acceso no autorizado',
            ], 401);
        }
    if ($request->has("api_key")) {
      $api_key = "6WugwSv7Ns3fbi51fHir48ckcpG1rKxW";
      if ($request->api_key != $api_key) {
        return response()->json([
          'status' => 401,
          'message' => 'Acceso no autorizado',
        ], 401);
      }
    }
        Stripe::setApiKey('sk_test_3RP8Mjx7h8bC6IeVcqOaSDFA');
        //crear
        $product = Product::create(array(
            'name' => $request->product_name,
            'type' => 'service',
        ));
        // si posee multiples optionces de membresia day, week, month or year
        $data = [];
        //simular optiones
        $opciones = $request->items;
        $items = [];
        foreach ($opciones as $option) {
            $price = Price::create(array(
                'unit_amount' => $option['price'],
                'currency'    => 'usd',
                'recurring'   => [
                    'interval'       => $option['interval'],
                    'interval_count' => $option['interval_count'],
                ],
                'product'     => $product->id
            ));
            $items[] = [
                "id" => $price->id
            ];
            usleep(500000);
        }
        $data = [
            'product' => $product->id,
            'tarifas' => $items,
        ];
        return \Response::json($data, 200);
  } catch (\Exception $ex) {
        $msg['response'] = [
            "error" => $ex->getMessage()
        ];
        return \Response::json($msg, 200);
    }
});
Route::post('subscription', function (Request $request) {
        try {
        if (!$request->has("api_key")) {
            return response()->json([
                'status'  => 401,
                'message' => 'Acceso no autorizado',
            ], 401);
        }
    if ($request->has("api_key")) {
      $api_key = "6WugwSv7Ns3fbi51fHir48ckcpG1rKxW";
      if ($request->api_key != $api_key) {
        return response()->json([
          'status' => 401,
          'message' => 'Acceso no autorizado',
        ], 401);
      }
    }
        Stripe::setApiKey('sk_test_3RP8Mjx7h8bC6IeVcqOaSDFA');
        //crear
        $customer = Customer::create(array(
        'source' => $request->token_stripe,
        ));
        $data = [];
        $subscription = Subscription::create(array(
            'customer' => $customer->id,
            'items'    => [
                ['price' => $request->item],
            ],
        ));
        $data = $subscription;
        return \Response::json($data, 200);
  } catch (\Exception $ex) {
        $msg['response'] = [
            "error" => $ex->getMessage()
        ];
        return \Response::json($msg, 200);
    }
});