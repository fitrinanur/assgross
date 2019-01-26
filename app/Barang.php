<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $fillable = ['kode_barang', 'no_faktur', 'qty', 'nama_barang'];

    public function getData()
    {
        $data = Barang::groupBy('no_faktur')->selectRaw('group_concat(nama_barang) as group_nama_barang, group_concat(kode_barang) as group_kode_barang')
            ->get()->map(function($row) {
                $group_nama_barang = explode(',',$row['group_nama_barang']);
                $group_kode_barang = explode(',',$row['group_kode_barang']);
                $data = [];
                foreach ($group_nama_barang as $key => $value) {
                    $value = trim($value);
                    $kode = trim($group_kode_barang[$key]);
                    $data[] = "$kode-$value";
                }
                return array_values(array_sort($data, function($value) {
                    return $value;
                }));
            })->toArray();
        return $data;
    }

    public function freqItem($min_sub)
    {
        $min_sub = $min_sub/100;
        $total_trans = Barang::groupBy('no_faktur')->get()->count();
        $data = Barang::selectRaw('count(kode_barang) as freq,kode_barang,nama_barang')->groupBy('kode_barang')->get();
        $result = $data->filter(function($value, $key) use ($total_trans, $min_sub) {
            return ($value['freq']/$total_trans) >= $min_sub;
        })->map(function($value, $key) use ($total_trans){
            $value['support'] = $value['freq'] / $total_trans;
            return $value;
        })->toArray();
        return $result;
    }
}
