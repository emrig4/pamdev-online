<?php
namespace App\Modules\Payment\Database\Seeders;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Modules\Payment\Helpers\ExchangeRateHelper;


/**
 *
 */
class ExchangeRateSeeder extends Seeder
{
      /**
        * Run the database seeds.
        *
        * @return void
     */
      public function run() {
           ExchangeRateHelper::getRates();
      }
}