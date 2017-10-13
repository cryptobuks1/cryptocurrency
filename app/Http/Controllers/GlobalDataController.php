<?php

namespace App\Http\Controllers;

use App\GlobalData;
use App\Search;
use Request;

class GlobalDataController extends Controller
{
    public function index()
    {
        $bitcoinPrice = GlobalData::findOrFail('bitcoin')->price_usd;
        $ethPrice = GlobalData::findOrFail('ethereum')->price_usd;
        $scriptJs = array("globalData.js", "calculator.js");
        return view('globalData.index', compact('bitcoinPrice', 'ethPrice', 'scriptJs'));
    }
    public function saveStatistic()
    {
        $input = Request::all();
        $rate = Search::find($input['id'])->rate;
        if($rate > 0) {
            $rate++;
            Search::where('id', $input['id'])->update(array('rate' => $rate));
        }
    }
}
