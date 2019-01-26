<?php

namespace App\Http\Controllers;

use App\Barang;
use App\Frequent;
use App\Setting;
use Illuminate\Http\Request;
use Phpml\Association\Apriori;

class SimulasiController extends Controller
{
    private $barang;
    private $setting;
    private $frequent;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Barang $barang, Setting $setting, Frequent $frequent)
    {
        // ambil data barang,setting,frequent
        //login only
        $this->middleware('auth');
        $this->barang = $barang;
        $this->setting = $setting;
        $this->frequent = $frequent;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['barangs'] = $this->barang->groupBy('kode_barang')->orderBy('kode_barang')->get();
        $predicts = [];
        if (session('result')['predicts']) {
            $predicts = session('result')['predicts'];
        }

        $data['predicts'] = $predicts;
        return view('pages.simulasi', $data);
    }

    public function proses(Request $request)
    {
        $barangs = $request->barang;

        $min_conf = $this->setting->find('min_conf')->value;
        $min_sup = $this->setting->find('min_sup')->value;
        $associator = new Apriori($min_sup / 100, $min_conf / 100);
        //get data sample dari import barang
        $associator->train($this->barang->getData(), []);
        // combination
        $predicts = [];
        $num = count($barangs);
        $total = pow(2, $num);
        for ($i = 0; $i < $total; $i++) {
            for ($j = 0; $j < $num; $j++) {
                if (pow(2, $j) & $i)
                    $combinations[$i][] = $barangs[$j];
            }
        }

        foreach ($combinations as $combination) {
            $result = $associator->predict($combination);
            if ($result) {
                $predicts[] = $result;
            }
        }

        $collections = collect($predicts)
            // merge array
            ->flatten()
            // remove duplicate items
            ->unique()
            // remove if item is exist in item request
            ->reject(function($value) use ($barangs){
                return in_array($value, $barangs);
            })
            ->mapWithKeys(function($value){
                $values = explode('-', $value);
                return [$values[0] => $values[1]];
            })
            ->all();
            
        $data = [
            'predicts' => $collections,
            'barangs' => $barangs,
            'freqs' => $this->frequent->get(),
            'min_conf' => $min_conf,
            'min_sup' => $min_sup,
            'iterations' => array_key_exists('frequent_support', $associator->debug) ? $associator->debug['frequent_support'] : [],
            'rules' => $associator->getRules()
        ];
        return redirect('simulasi')->with('result', $data);
    }
}
