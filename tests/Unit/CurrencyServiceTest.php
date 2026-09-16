<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyServiceTest extends TestCase
{
    protected CurrencyService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CurrencyService();
        Cache::flush();
    }

    public function test_default_currency_is_idr()
    {
        session()->forget('user_currency');
        $this->assertEquals('IDR', $this->service->getUserCurrency());
    }

    public function test_can_set_and_get_user_currency()
    {
        $this->assertTrue($this->service->setUserCurrency('USD'));
        $this->assertEquals('USD', $this->service->getUserCurrency());

        $this->assertTrue($this->service->setUserCurrency('IDR'));
        $this->assertEquals('IDR', $this->service->getUserCurrency());

        // Invalid currency rejected and keeps previous
        $this->assertFalse($this->service->setUserCurrency('XYZ'));
        $this->assertEquals('IDR', $this->service->getUserCurrency());
    }

    public function test_conversions_and_formatting()
    {
        // Mock API response
        Http::fake([
            'https://api.frankfurter.dev/v2/rate/usd/idr' => Http::response([
                'base' => 'USD',
                'quote' => 'IDR',
                'rate' => 17500,
                'date' => '2026-09-13',
            ], 200),
        ]);

        $rate = $this->service->getUsdToIdrRate();
        $this->assertEquals(17500.0, $rate);

        // IDR to USD
        $usd = $this->service->convert(175000000, 'IDR', 'USD');
        $this->assertEquals(10000.0, $usd);

        // USD to IDR
        $idr = $this->service->convert(10000, 'USD', 'IDR');
        $this->assertEquals(175000000.0, $idr);

        // Formatting
        $this->assertEquals('Rp 175.000.000', $this->service->format(175000000, 'IDR'));
        $this->assertEquals('$10,000', $this->service->format(10000, 'USD'));

        // formatPropertyPrice in IDR vs USD
        $this->service->setUserCurrency('IDR');
        $this->assertEquals('Rp 175.000.000', $this->service->formatPropertyPrice(175000000));

        $this->service->setUserCurrency('USD');
        $this->assertEquals('$10,000', $this->service->formatPropertyPrice(175000000));
    }

    public function test_api_failure_fallback_to_cached_rate()
    {
        // First successful fetch
        Http::fake([
            'https://api.frankfurter.dev/v2/rate/usd/idr' => Http::response([
                'base' => 'USD',
                'quote' => 'IDR',
                'rate' => 17500,
                'date' => '2026-09-13',
            ], 200),
        ]);
        $this->assertEquals(17500, $this->service->getUsdToIdrRate());

        // Clear only the 1-hour primary cache, simulate API outage
        Cache::forget('currency_rate_USD_IDR');
        Http::fake([
            '*' => Http::response(null, 500),
        ]);

        // Should retrieve from last known persistent fallback cache without crash
        $fallbackRate = $this->service->getUsdToIdrRate();
        $this->assertEquals(17500, $fallbackRate);
    }

    public function test_total_api_outage_safely_falls_back_to_idr_without_crash()
    {
        // Mock CurrencyService to simulate null rate (total outage without baseline)
        $service = $this->getMockBuilder(CurrencyService::class)
            ->onlyMethods(['getUsdToIdrRate'])
            ->getMock();
        $service->method('getUsdToIdrRate')->willReturn(null);

        $service->setUserCurrency('USD');
        // If conversion is impossible, falls back to canonical IDR format safely
        $formatted = $service->formatPropertyPrice(5000000000);
        $this->assertEquals('Rp 5.000.000.000', $formatted);
    }

    public function test_filter_idr_bounds_conversion()
    {
        Http::fake([
            'https://api.frankfurter.dev/v2/rate/usd/idr' => Http::response([
                'base' => 'USD',
                'quote' => 'IDR',
                'rate' => 17500,
                'date' => '2026-09-13',
            ], 200),
        ]);

        // Range keys
        $bounds = $this->service->getFilterIdrBounds('under_2b');
        $this->assertNull($bounds['min']);
        $this->assertEquals(2000000000, $bounds['max']);

        $bounds2 = $this->service->getFilterIdrBounds('2b_to_5b');
        $this->assertEquals(2000000000, $bounds2['min']);
        $this->assertEquals(5000000000, $bounds2['max']);

        $bounds3 = $this->service->getFilterIdrBounds('above_5b');
        $this->assertEquals(5000000000, $bounds3['min']);
        $this->assertNull($bounds3['max']);

        // USD min/max bounds translated to IDR
        $usdBounds = $this->service->getFilterIdrBounds(null, 100000, 200000, 'USD');
        $this->assertEquals(1750000000.0, $usdBounds['min']);
        $this->assertEquals(3500000000.0, $usdBounds['max']);
    }
}
