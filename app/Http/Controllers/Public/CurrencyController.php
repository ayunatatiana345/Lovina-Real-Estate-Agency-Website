<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    protected CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Switch currency and redirect back with all previous query parameters intact.
     */
    public function switchCurrency(Request $request, string $currency)
    {
        $this->currencyService->setUserCurrency($currency);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'currency' => $this->currencyService->getUserCurrency(),
                'metadata' => $this->currencyService->getRateMetadata(),
            ]);
        }

        return redirect()->back();
    }

    /**
     * API endpoint to get current currency metadata.
     */
    public function getMeta()
    {
        return response()->json($this->currencyService->getRateMetadata());
    }
}
