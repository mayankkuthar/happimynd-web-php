<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    public const ADMIN_HOME = 'admin/dashboard';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            // HappiMynd Games Hub (intern project) — its own middleware group,
            // namespaced under /api/hub to match the existing /api/v1/* APIs.
            Route::prefix('api/hub')
                ->middleware('hub')
                ->group(base_path('routes/hub.php'));

            // Auth endpoints live under /api/hub/auth/*.
            Route::prefix('api/hub/auth')
                ->middleware('hub')
                ->group(base_path('routes/hub_auth.php'));

            // HR Auto (Resume Extractor) — intern project, own MySQL database.
            Route::prefix('api/hr')
                ->middleware('hr')
                ->group(base_path('routes/hr.php'));

            // Invoice Platform — intern project, own MySQL database.
            Route::prefix('api/invoice')
                ->middleware('invoice')
                ->group(base_path('routes/invoice.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            $key = optional($request->user())->id ?: $request->ip();

            // The HappiLIFE assessment saves every answer as it is tapped, so a
            // user answering quickly used up the shared 60/min allowance and
            // then every other screen failed with 429 for up to a minute. The
            // assessment endpoints get their own, larger allowance.
            if ($request->is(
                'api/v1/start-assessment',
                'api/v1/save-option',
                'api/v1/update-last-answer',
                'api/v1/assessment-status',
                'api/v1/checkifany'
            )) {
                return Limit::perMinute(300)->by('assessment|' . $key);
            }

            return Limit::perMinute(60)->by($key);
        });
    }
}
