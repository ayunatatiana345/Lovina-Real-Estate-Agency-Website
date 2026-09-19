<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\CompanySetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('settings', CompanySetting::getSettings());
        });

        \Carbon\Carbon::macro('toWitaFormat', function () {
            /** @var \Carbon\Carbon $this */
            return $this->copy()->setTimezone('Asia/Makassar')->format('d M Y, h:i A \W\I\T\A');
        });

        \Illuminate\Support\Facades\Blade::directive('wita', function ($expression) {
            return "<?php echo ($expression) ? \\Carbon\\Carbon::parse($expression)->setTimezone('Asia/Makassar')->format('d M Y, h:i A \\W\\I\\T\\A') : '-'; ?>";
        });
    }
}
