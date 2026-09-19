<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    public const BASE_CURRENCY = 'IDR';
    public const CACHE_TTL_SECONDS = 3600; // 1 hour cache

    public const SUPPORTED_CURRENCIES = [
        'IDR' => [
            'code' => 'IDR',
            'symbol' => 'Rp',
            'name' => 'Indonesian Rupiah',
            'label' => 'IDR (Rp)',
        ],
        'USD' => [
            'code' => 'USD',
            'symbol' => '$',
            'name' => 'US Dollar',
            'label' => 'USD ($)',
        ],
    ];

    protected ?string $runtimeCurrency = null;

    /**
     * Get the active user currency from session or runtime fallback, defaulting to IDR.
     */
    public function getUserCurrency(): string
    {
        if ($this->runtimeCurrency !== null) {
            return $this->runtimeCurrency;
        }

        if (app()->bound('session')) {
            try {
                $curr = strtoupper((string) session('user_currency', self::BASE_CURRENCY));
                return array_key_exists($curr, self::SUPPORTED_CURRENCIES) ? $curr : self::BASE_CURRENCY;
            } catch (\Throwable $e) {
                // Ignore and fall back to BASE_CURRENCY
            }
        }

        return self::BASE_CURRENCY;
    }

    /**
     * Set the active user currency in session and runtime memory.
     */
    public function setUserCurrency(string $currency): bool
    {
        $currency = strtoupper(trim($currency));
        if (array_key_exists($currency, self::SUPPORTED_CURRENCIES)) {
            $this->runtimeCurrency = $currency;
            if (app()->bound('session')) {
                try {
                    session(['user_currency' => $currency]);
                } catch (\Throwable $e) {
                    // Ignore session write failure in CLI
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Get the exchange rate for USD to IDR (1 USD = X IDR).
     * Returns rate or null if completely unavailable.
     */
    public function getUsdToIdrRate(): ?float
    {
        $cacheKey = 'currency_rate_USD_IDR';
        $fallbackKey = 'currency_rate_last_known_USD_IDR';

        // 1. Check primary cache (1 hour)
        $cachedData = Cache::get($cacheKey);
        if ($this->isValidRateData($cachedData, 'USD', 'IDR')) {
            return (float) $cachedData['rate'];
        }

        // 2. Fetch fresh rate from external API
        $fetched = $this->fetchRateFromApi('USD', 'IDR');
        if ($this->isValidRateData($fetched, 'USD', 'IDR')) {
            Cache::put($cacheKey, $fetched, self::CACHE_TTL_SECONDS);
            Cache::forever($fallbackKey, $fetched);
            $this->storeDurableFallback('USD', 'IDR', $fetched);
            return (float) $fetched['rate'];
        }

        // 3. Fallback to last known successfully cached rate
        $lastKnown = Cache::get($fallbackKey);
        if ($this->isValidRateData($lastKnown, 'USD', 'IDR')) {
            return (float) $lastKnown['rate'];
        }

        // 4. Fallback to durable file/storage backup
        $durable = $this->getDurableFallback('USD', 'IDR');
        if ($this->isValidRateData($durable, 'USD', 'IDR')) {
            return (float) $durable['rate'];
        }

        // Safe baseline fallback rate if network is down on first install (e.g. 17,500 IDR / 1 USD)
        return 17500.0;
    }

    /**
     * Fetch rate from external Frankfurter API with strict validation.
     */
    protected function fetchRateFromApi(string $base = 'USD', string $quote = 'IDR'): ?array
    {
        $base = strtoupper($base);
        $quote = strtoupper($quote);

        // Try primary endpoint: Frankfurter v2 rate endpoint (https://api.frankfurter.dev/v2/rate/usd/idr)
        try {
            $url = "https://api.frankfurter.dev/v2/rate/" . strtolower($base) . "/" . strtolower($quote);
            $response = Http::timeout(5)->acceptJson()->get($url);

            if ($response->successful()) {
                $data = $response->json();
                if ($this->isValidV2ApiResponse($data, $base, $quote)) {
                    return [
                        'base' => $base,
                        'quote' => $quote,
                        'rate' => (float) $data['rate'],
                        'date' => $data['date'],
                        'provider' => 'Frankfurter v2 (European Central Bank reference)',
                        'fetched_at' => now()->toIso8601String(),
                        'source_url' => $url,
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning("CurrencyService primary Frankfurter v2 API failed: " . $e->getMessage());
        }

        // Secondary fallback endpoint: Frankfurter v1 latest
        try {
            $response = Http::timeout(5)->acceptJson()->get("https://api.frankfurter.app/latest", [
                'from' => $base,
                'to' => $quote,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($this->isValidV1ApiResponse($data, $base, $quote)) {
                    return [
                        'base' => $base,
                        'quote' => $quote,
                        'rate' => (float) $data['rates'][$quote],
                        'date' => $data['date'] ?? date('Y-m-d'),
                        'provider' => 'Frankfurter v1 (European Central Bank reference)',
                        'fetched_at' => now()->toIso8601String(),
                        'source_url' => 'https://api.frankfurter.app/latest',
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning("CurrencyService secondary Frankfurter v1 API fallback failed: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Validate Frankfurter v2 rate response.
     */
    protected function isValidV2ApiResponse($data, string $expectedBase, string $expectedQuote): bool
    {
        if (!is_array($data)) {
            return false;
        }

        // Verify base currency
        if (isset($data['base']) && strtoupper((string)$data['base']) !== strtoupper($expectedBase)) {
            return false;
        }

        // Verify quote currency
        if (isset($data['quote']) && strtoupper((string)$data['quote']) !== strtoupper($expectedQuote)) {
            return false;
        }

        // Verify rate exists, is numeric, and strictly positive
        if (!isset($data['rate']) || !is_numeric($data['rate']) || (float)$data['rate'] <= 0) {
            return false;
        }

        // Verify date format (YYYY-MM-DD)
        if (!isset($data['date']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$data['date'])) {
            return false;
        }

        return true;
    }

    /**
     * Validate Frankfurter v1 rates response.
     */
    protected function isValidV1ApiResponse($data, string $expectedBase, string $expectedQuote): bool
    {
        if (!is_array($data)) {
            return false;
        }

        if (isset($data['base']) && strtoupper((string)$data['base']) !== strtoupper($expectedBase)) {
            return false;
        }

        if (!isset($data['rates'][$expectedQuote]) || !is_numeric($data['rates'][$expectedQuote]) || (float)$data['rates'][$expectedQuote] <= 0) {
            return false;
        }

        return true;
    }

    /**
     * Validate normalized internal rate payload.
     */
    protected function isValidRateData($data, string $expectedBase, string $expectedQuote): bool
    {
        return is_array($data)
            && isset($data['rate'])
            && is_numeric($data['rate'])
            && (float) $data['rate'] > 0
            && (!isset($data['base']) || strtoupper($data['base']) === strtoupper($expectedBase))
            && (!isset($data['quote']) || strtoupper($data['quote']) === strtoupper($expectedQuote));
    }

    /**
     * Store rate payload in durable storage backup.
     */
    protected function storeDurableFallback(string $base, string $quote, array $data): void
    {
        try {
            $path = storage_path('app/currency_rates.json');
            $dir = dirname($path);
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            $allRates = [];
            if (file_exists($path)) {
                $allRates = json_decode(file_get_contents($path), true) ?: [];
            }
            $allRates["{$base}_{$quote}"] = $data;
            file_put_contents($path, json_encode($allRates, JSON_PRETTY_PRINT));
        } catch (\Throwable $e) {
            // Ignore file storage errors safely
        }
    }

    /**
     * Retrieve rate payload from durable storage backup.
     */
    protected function getDurableFallback(string $base, string $quote): ?array
    {
        try {
            $path = storage_path('app/currency_rates.json');
            if (file_exists($path)) {
                $allRates = json_decode(file_get_contents($path), true) ?: [];
                $key = "{$base}_{$quote}";
                if (isset($allRates[$key]) && is_array($allRates[$key])) {
                    return $allRates[$key];
                }
            }
        } catch (\Throwable $e) {
            // Ignore file storage errors safely
        }
        return null;
    }

    /**
     * Convert an amount from one currency to another.
     */
    public function convert(float $amount, string $from = 'IDR', string $to = 'USD'): ?float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        if ($from === $to) {
            return $amount;
        }

        $usdToIdrRate = $this->getUsdToIdrRate();
        if ($usdToIdrRate === null || $usdToIdrRate <= 0) {
            return null;
        }

        if ($from === 'IDR' && $to === 'USD') {
            return $amount / $usdToIdrRate;
        }

        if ($from === 'USD' && $to === 'IDR') {
            return $amount * $usdToIdrRate;
        }

        return null;
    }

    /**
     * Format a numeric amount according to currency rules.
     */
    public function format(float $amount, string $currency = 'IDR'): string
    {
        $currency = strtoupper($currency);

        if ($currency === 'USD') {
            return '$' . number_format(round($amount), 0, '.', ',');
        }

        // Standard Indonesian Rupiah formatting
        return 'Rp ' . number_format(round($amount), 0, ',', '.');
    }

    /**
     * Format a canonical IDR property price into the target (or active user) currency.
     */
    public function formatPropertyPrice(?float $canonicalPriceIdr, ?string $targetCurrency = null): string
    {
        if ($canonicalPriceIdr === null || $canonicalPriceIdr <= 0) {
            return 'Price on Request';
        }

        $targetCurrency = $targetCurrency ? strtoupper($targetCurrency) : $this->getUserCurrency();

        if ($targetCurrency === 'USD') {
            $converted = $this->convert($canonicalPriceIdr, 'IDR', 'USD');
            if ($converted !== null) {
                return $this->format($converted, 'USD');
            }
        }

        // Default / fallback to canonical IDR
        return $this->format($canonicalPriceIdr, 'IDR');
    }

    /**
     * Return metadata about the current active exchange rate for UI display.
     */
    public function getRateMetadata(): array
    {
        $rate = $this->getUsdToIdrRate();
        $cached = Cache::get('currency_rate_USD_IDR');
        $fallback = Cache::get('currency_rate_last_known_USD_IDR');
        $durable = $this->getDurableFallback('USD', 'IDR');

        $payload = $cached ?? $fallback ?? $durable;

        $status = 'Connected / Live Cached Rate';
        if ($cached) {
            $status = 'Connected / Using Cached Rate';
        } elseif ($fallback || $durable) {
            $status = 'Connected / Using Cached Fallback';
        } elseif ($rate) {
            $status = 'Connected / Standard Reference Rate';
        } else {
            $status = 'Unavailable';
        }

        return [
            'active_currency' => $this->getUserCurrency(),
            'supported_currencies' => self::SUPPORTED_CURRENCIES,
            'usd_to_idr_rate' => $rate,
            'formatted_rate' => $rate ? '1 USD = Rp ' . number_format($rate, 0, ',', '.') : null,
            'date' => $payload['date'] ?? date('Y-m-d'),
            'rate_date' => isset($payload['date']) ? \Carbon\Carbon::parse($payload['date'])->format('d M Y') : date('d M Y'),
            'provider' => $payload['provider'] ?? 'Frankfurter (European Central Bank reference)',
            'fetched_at' => $payload['fetched_at'] ?? now()->toIso8601String(),
            'last_updated' => isset($payload['fetched_at']) ? \Carbon\Carbon::parse($payload['fetched_at'])->setTimezone('Asia/Makassar')->format('d M Y, h:i A \W\I\T\A') : now()->setTimezone('Asia/Makassar')->format('d M Y, h:i A \W\I\T\A'),
            'status' => $status,
            'is_available' => ($rate !== null && $rate > 0),
        ];
    }

    /**
     * Return localized price range options for filter dropdowns.
     */
    public function getPriceRangeOptions(?string $currency = null): array
    {
        $currency = $currency ? strtoupper($currency) : $this->getUserCurrency();
        $rate = $this->getUsdToIdrRate();

        if ($currency === 'USD' && $rate !== null && $rate > 0) {
            $usd2b = round((2000000000 / $rate) / 1000) * 1000;
            $usd5b = round((5000000000 / $rate) / 1000) * 1000;

            return [
                '' => 'Any Price',
                'under_2b' => 'Under $' . number_format($usd2b, 0, '.', ','),
                '2b_to_5b' => '$' . number_format($usd2b, 0, '.', ',') . ' – $' . number_format($usd5b, 0, '.', ','),
                'above_5b' => 'Above $' . number_format($usd5b, 0, '.', ','),
            ];
        }

        return [
            '' => 'Any Price',
            'under_2b' => 'Under IDR 2 Billion',
            '2b_to_5b' => 'IDR 2 Billion – IDR 5 Billion',
            'above_5b' => 'Above IDR 5 Billion',
        ];
    }

    /**
     * Convert standard price range key or min/max boundaries to canonical IDR boundaries.
     */
    public function getFilterIdrBounds(?string $rangeKey, ?float $min = null, ?float $max = null, ?string $currency = null): array
    {
        $currency = $currency ? strtoupper($currency) : $this->getUserCurrency();
        $minIdr = null;
        $maxIdr = null;

        if (!empty($rangeKey)) {
            switch ($rangeKey) {
                case 'under_2b':
                    $minIdr = null;
                    $maxIdr = 2000000000;
                    break;
                case '2b_to_5b':
                    $minIdr = 2000000000;
                    $maxIdr = 5000000000;
                    break;
                case 'above_5b':
                    $minIdr = 5000000000;
                    $maxIdr = null;
                    break;
            }
        } elseif ($min !== null || $max !== null) {
            if ($currency === 'USD') {
                $minIdr = $min !== null ? $this->convert($min, 'USD', 'IDR') : null;
                $maxIdr = $max !== null ? $this->convert($max, 'USD', 'IDR') : null;
            } else {
                $minIdr = $min;
                $maxIdr = $max;
            }
        }

        return [
            'min' => $minIdr !== null ? (float) $minIdr : null,
            'max' => $maxIdr !== null ? (float) $maxIdr : null,
        ];
    }
}
