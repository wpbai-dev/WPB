<?php

namespace App\Http\Controllers\Payments;

use App\Events\TransactionPaid;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Vironeer\NOWPayments\Authentication;
use Vironeer\NOWPayments\Client;

class NowpaymentsController extends Controller
{
    private $paymentGateway;
    private $apiKey;
    private $client;
    private $baseUri;

    public function __construct()
    {
        $this->paymentGateway = paymentGateway('nowpayments');
        $this->client = new Client(
            $this->paymentGateway->credentials->api_key,
            $this->paymentGateway->isSandboxMode()
        );
    }

    public function process($trx)
    {
        try {
            $body = [
                'order_id' => $trx->id,
                'order_description' => translate('Payment for order #:number', [
                    'number' => $trx->id,
                ]),
                'price_amount' => $trx->total,
                'price_currency' => @settings('currency')->code,
                'ipn_callback_url' => route('payments.webhooks.nowpayments'),
                'success_url' => route('payments.ipn.nowpayments', ['id' => hash_encode($trx->id)]),
                'cancel_url' => route('checkout.index', hash_encode($trx->id)),
            ];

            $invoice = $this->client->invoice->create($body);

            $trx->payment_id = $invoice['id'];
            $trx->update();

            $data['type'] = "success";
            $data['method'] = "redirect";
            $data['redirect_url'] = $invoice['invoice_url'];
        } catch (\Exception $e) {
            $data['type'] = "error";
            $data['msg'] = $e->getMessage();
        }

        return json_encode($data);
    }

    public function ipn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required'],
            'NP_id' => ['required'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                toastr()->error($error);
            }
            return redirect()->route('home');
        }

        $trx = Transaction::where('id', hash_decode($request->id))
            ->where('user_id', authUser()->id)
            ->whereIn('status', [Transaction::STATUS_PAID, Transaction::STATUS_UNPAID])
            ->firstOrFail();

        $checkoutLink = route('checkout.index', hash_encode($trx->id));

        if ($trx->isPaid()) {
            $trx->user->emptyCart();
            return redirect($checkoutLink);
        }

        try {
            $payment = $this->client->payment->get($request->NP_id);

            if ($payment['payment_status'] == "finished") {
                $trx->payment_id = $payment['payment_id'];
                $trx->status = Transaction::STATUS_PAID;
                $trx->update();

                $trx->user->emptyCart();
                event(new TransactionPaid($trx));
            }

            return redirect($checkoutLink);
        } catch (\Exception $e) {
            toastr()->error($e->getMessage());
            return redirect($checkoutLink);
        }
    }

    public function webhook(Request $request)
    {
        try {
            $authentication = Authentication::authenticate(
                $request->getContent(),
                $request->header('x-nowpayments-sig'),
                $this->paymentGateway->credentials->ipn_secret_key
            );

            if (!$authentication) {
                return response('Invalid signature', 401);
            }

            $payload = $request->all();

            if (!$payload) {
                return response('Invalid payload', 401);
            }

            if ($payload['payment_status'] == "finished") {
                $trx = Transaction::where('id', $payload['order_id'])
                    ->where('payment_id', $payload['invoice_id'])
                    ->unpaid()->first();

                if ($trx) {
                    $trx->payment_id = $payload['payment_id'];
                    $trx->status = Transaction::STATUS_PAID;
                    $trx->update();
                    event(new TransactionPaid($trx));
                }
            }

            return response('Webhook processed successfully', 200);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }
}