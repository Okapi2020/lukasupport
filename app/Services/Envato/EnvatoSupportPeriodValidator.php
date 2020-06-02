<?php

namespace App\Services\Envato;

use App;
use App\PurchaseCode;
use Auth;
use Carbon\Carbon;
use Common\Settings\Settings;
use Request;

class EnvatoSupportPeriodValidator
{
    /**
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return bool
     */
    public function validate($attribute, $value, $parameters) {
        if (app(Settings::class)->get('envato.active_support')) {
            $purchaseCodes = app(PurchaseCode::class)
                ->where('user_id', Request::get('user_id', Auth::id()))
                ->get();
            $supportExpired = $purchaseCodes->contains(function(PurchaseCode $code) {
                return $code->supported_until && $code->supported_until->lessThan(Carbon::now());
            });
            return !$supportExpired;
        } else {
            return true;
        }
    }

}
