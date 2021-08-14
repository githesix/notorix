<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Paginator::useTailwind();
        
        Blade::if('xrole', function ($bit) {
            if (Auth::check()) {
                return auth()->user()->role & $bit;
            }
        });
        
        Blade::if('xgroupe', function ($nom) {
            if (Auth::check()) {
                return auth()->user()->estMembreDe($nom);
            }
        });
        
        Blade::directive('euro', function ($cents) {
            return "<?php echo '€&nbsp;'. number_format($cents/100,2) ; ?>";
        });
        
        Blade::directive('joliedate', function ($laidedate) {
            /*if ($laidedate instanceof \DateTime) {
                $expression = $laidedate;
            } else {
                $expression = \Carbon\Carbon::parse($laidedate);
            }*/
            return "<?php echo ($laidedate)->format('d/m/Y H:i'); ?>";
        });

        Blade::directive('mf', function ($mif) {
            $mif = trim($mif, '"\'');
            list($m, $f) = explode('|', $mif);
            $m = htmlspecialchars($m);
            $f = htmlspecialchars($f);
            return "<?php if (\Illuminate\Support\Facades\Auth::check()) { echo auth()->user()-> sexe == 'm' ? '$m' : '$f';} else {echo '$m';} ?>";
        });

    }
}
