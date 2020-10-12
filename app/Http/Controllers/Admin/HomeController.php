<?php

namespace App\Http\Controllers\Admin;

use App\Models\Osoba;
use App\Models\OsobaSi;
use Illuminate\Routing\Controller;

class HomeController extends Controller {
    protected $data = []; // the information we send to the view

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        $this->middleware(backpack_middleware());
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard() {
        $this->data['title'] = trans('backpack::base.dashboard'); // set the page title
        /*        $this->data['breadcrumbs'] = [
                    trans('backpack::crud.admin')     => backpack_url('dashboard'),
                    trans('backpack::base.dashboard') => false,
                ];*/
//dd($this->data);

        return view(backpack_view('home'), $this->data);
    }

    /**
     * Redirect to the dashboard.
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function redirect() {
        // The '/admin' route is not to be used as a page, because it breaks the menu's active state.
        return redirect(backpack_url('dashboard'));
    }

    public function test() {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', '7200');
//        $br = "\n";
        $br = "<br>";
        $this->data['osobaSiKojihNemauOsoba'] = 0;
        echo $br . "Start time: " . now();
        echo $br . "Memorija start: " . $this->convert(memory_get_usage(false)) . $br;
        $time = $this->measureTime();

        $osobeSi = OsobaSi::whenNotEmpty('ime')->whenNotEmpty('prezime')->get();
/*        ->chunk(500, function ($osobeSi) {
            foreach ($osobeSi as $osobaSi) {
                $osoba = Osoba::find($osobaSi->id);
                if (is_null($osoba)) {
//                $osoba = Osoba::create($osobaSi->toArray());
                    $this->data['osobaSiKojihNemauOsoba']++;
                }
            }
            if ($this->data['osobaSiKojihNemauOsoba'] % 100 == 0) {
                echo "\nChunk: $this->data['osobaSiKojihNemauOsoba'] | " . $this->convert(memory_get_usage());
            }
        });*/

        echo $br . "Vreme izvrsavanja: " . $this->measureTime($time) . "sec";
        echo $br . "Max memorija: " . $this->convert(memory_get_peak_usage(true));
        dd($this->data['osobaSiKojihNemauOsoba']);
    }

    protected
    function convert($size) {

        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }

    public
    function measureTime($start = false) {
        if ($start === false) {
            //start
            return (int)microtime(true);
        } else {
            //stop
            return (int)microtime(true) - (int)$start;
        }
    }
}
