<?php

if( !function_exists('theme_asset') )
{
    function theme_asset($path, $secure = null)
    {
        $activeTheme = config('themes')['active'];
        $siteUrl = URL::to('');
        return  $siteUrl . '/themes/' . $activeTheme . '/' . $path;
    }
}

if(! function_exists('translate')){
	function translate($string){
		$tr = new Stichoza\GoogleTranslate\GoogleTranslate();
		//$tr->setSource('en'); // Translate from English
		$tr->setSource(); // Detect language automatically
		$tr->setTarget('ka'); // Translate to Georgian
		return $tr->translate($string);
	}
}


if(! function_exists('ranc_equivalent')){
	function ranc_equivalent($fiat_amount, $currency){

		$ngn_rate = setting('ngn_rate') ?? App\Modules\Payment\Models\Currency::whereCode('NGN')->first()->rate;
		$usd_rate = setting('usd_rate') ?? App\Modules\Payment\Models\Currency::whereCode('USD')->first()->rate;
		$ranc_rate = setting('ranc_rate') ??  App\Modules\Payment\Models\Currency::whereCode('RANC')->first()->rate;

		if($currency == 'NGN'){
			return ($fiat_amount/ $ngn_rate ) * $ranc_rate ;
		}else{
			return ($fiat_amount/ $usd_rate ) * $ranc_rate ;
		}
		
	}
}


if(! function_exists('currency_exchange')){
	function currency_exchange($amount, $from_currency = null,  $to_currency = null){

		$ngn_rate = setting('ngn_rate') ??  App\Modules\Payment\Models\Currency::whereCode('NGN')->first()->rate;
		$usd_rate = setting('usd_rate') ?? App\Modules\Payment\Models\Currency::whereCode('USD')->first()->rate;
		$ranc_rate = setting('ranc_rate') ?? App\Modules\Payment\Models\Currency::whereCode('RANC')->first()->rate;


		if($from_currency == 'NGN' && $to_currency == 'USD'){

			$result =  ($amount / $ngn_rate ) ;
			return (float) number_format( $result, 2, '.', '' );
			
		}

		if($from_currency == 'USD' && $to_currency == 'NGN'){
			return ($amount * $ngn_rate ) ;
		}

		if($from_currency == 'RANC' && $to_currency == 'USD'){
			$result = ($amount/$ranc_rate ) ;
			return (float) number_format( $result, 2, '.', '' );
		}

		if($from_currency == 'USD' && $to_currency == 'RANC'){
			return ($amount * $ranc_rate ) ;
		}

		if($from_currency == 'NGN' && $to_currency == 'RANC'){
			return ( ($amount/$ngn_rate) * $ranc_rate ) ;
		}

		if($from_currency == 'RANC' && $to_currency == 'NGN'){
			return ( ($amount/$ranc_rate) * $ngn_rate ) ;
		}

		return $amount ;
		
	}
}


if(! function_exists('fiat_equivalent')){
	function fiat_equivalent($ranc_amount, $currency){

		$ngn_rate = setting('ngn_rate') ?? App\Modules\Payment\Models\Currency::whereCode('NGN')->first()->rate;
		$usd_rate =  setting('usd_rate') ?? App\Modules\Payment\Models\Currency::whereCode('USD')->first()->rate;
		$ranc_rate = setting('ranc_rate') ?? App\Modules\Payment\Models\Currency::whereCode('RANC')->first()->rate;

		 if($currency === 'NGN') {
            return  floor(  ( $ranc_amount/100 ) * $ngn_rate  );  //convert cent to dollar then to naira
        }else{
            return (float) number_format( $ranc_amount / $ranc_rate, 2, '.', '' ); //convert cent to dollar
        }	
	}
}

