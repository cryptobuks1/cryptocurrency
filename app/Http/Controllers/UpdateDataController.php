<?php

namespace App\Http\Controllers;

use App\ExchangeRate;
use App\CoinMarketCap;
use App\GlobalData;
use App\ExchangeRatesCap;
use App\Search;
use App\TotalMarketCap;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateDataController extends Controller
{
    /**
     * Store all data from the coinmarketcap
     *
     * @param  int $id
     * @return Response
     */
    public function storeAllFrom()
    {
        $data = CoinMarketCap\Base::getGlobalData();
        $TotalMarketCap = 0;
        foreach ($data as $key => $item) {
            $TotalMarketCap += $item['market_cap_usd'];
            GlobalData::updateOrCreate(['id' => "{$item['id']}"], ['name' => "{$item['name']}", 'symbol' => "{$item['symbol']}", 'rank' => "{$item['rank']}", 'price_usd' => $this->updateValue($item['price_usd']), 'price_btc' => $this->updateValue($item['price_btc']), 'volume_usd_24h' => $this->updateValue($item['24h_volume_usd']), 'market_cap_usd' => $this->updateValue($item['market_cap_usd']), 'available_supply' => $this->updateValue($item['available_supply']), 'total_supply' => $this->updateValue($item['total_supply']), 'percent_change_1h' => $this->updateValue($item['percent_change_1h']), 'percent_change_24h' => $this->updateValue($item['percent_change_24h']), 'percent_change_7d' => $this->updateValue($item['percent_change_7d']), 'last_updated' => $this->updateValue($item['last_updated'])]);
            $tableName = str_replace(' ', '-', $item['id']);

            if (!Schema::connection('mysql2')->hasTable($tableName)) {
                $tableName = str_replace(' ', '-', $item['name']);
                if (!Schema::connection('mysql2')->hasTable($tableName)) {
                    try {
                        $tableName = str_replace(' ', '-', $item['id']);
                        $this->tryCreateTable($tableName);
                    } catch (\Illuminate\Database\QueryException $e) {
                        $tableName = str_replace(' ', '-', $item['name']);
                        $this->tryCreateTable($tableName);
                    } catch (PDOException $e) {
                        dd($e);
                    }
                }
            }
            $this->insertToHistoryDB($tableName, $item);
        }
        TotalMarketCap::updateOrCreate(['id' => 1 ], ['price' => $TotalMarketCap]);
        $this->updateSearchTable();
        return 'Updated successfully';
    }
    /**
     * Store all data from the ExchangeRates
     *
     * @return Response
     */
    public static function storeExchangeRates()
    {
        $data = ExchangeRatesCap\Base::getExchangeRates();
        foreach ($data['quotes'] as $key => $item) {
            if ($key != "USDBTC") {

                $oldItem = ExchangeRate::where('name_quotes', 'like', $key)->get()->first();
                if ($oldItem === null) {
                    $exchange = new ExchangeRate;

                    $exchange->name_quotes = $key;
                    $exchange->value_quotes = $item;
                    $exchange->value_quotesOld = $item;
                    $exchange->source = $data['source'];

                    $exchange->save();

                    //need to creqate new one;
                } else {
                    //need to get old value_quotes an put to value_quotesOld
                    //put new value_quotes
                    $oldItem->value_quotesOld = $oldItem->value_quotes;
                    $oldItem->value_quotes = $item;
                    $oldItem->source = $data['source'];
                    $oldItem->save();
                }
            }
        }
        self::updateSearchTable();
        return 'Updated successfully';
    }
    function updateValue($number) {
        $value = rand(0,1) == 1;
        if($value) {
            $number = $number + ($number * 0.05 / 100);
        } else {
            $number = $number - ($number * 0.05 / 100);
        }
        return $number;
    }
    public static function updateSearchTable() {
        $listElements = Search\Base::generateListElements();
        foreach ($listElements as $listElement) {
            Search::updateOrCreate(
                [
                    'id' => "{$listElement['id']}"
                ],[
                    "price_usd" => $listElement['price_usd'],
                    "type" => $listElement['type'],
                    "profile_long" => $listElement['profile_long'],
                ]
            );
        }

    }
    function tryCreateTable($tableName) {
        Schema::connection('mysql2')->create(quotemeta($tableName), function ($table) {
            $table->increments('id');
            $table->double('price_usd');
            $table->dateTime('created_at');
        });
        return true;
    }

    function insertToHistoryDB($tableName, $data)
    {
        DB::connection('mysql2')->table(quotemeta($tableName))->insert(['price_usd' => $data['price_usd'], 'created_at' => Carbon::now()]);
    }
}
