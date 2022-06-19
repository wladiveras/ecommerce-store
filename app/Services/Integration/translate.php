<?php

	namespace App\Services\Integration;
	use App\Models\Order;
	use App\Models\PricingRole;

	class Translate {
		public static function run($type, $value)
		{
			switch($type)
			{
				case "shipping" :
					return Translate::_translate_shipping($value);
                break;
                case "payment" :
					return Translate::_translate_payment($value);
                break;
                case "pricing" :
					return Translate::_translate_princing_role($value);
				break;
				default : 
					return $value;
				break;
			}
		}

		public static function _translate_shipping($value)
		{
			switch($value)
			{
				case "payment" :
					return "Retirada";
				break;
				default : 
					return $value;
				break;
			}
        }
        
        public static function _translate_payment($value)
		{
			switch($value)
			{
				case "paylater" :
					return "Pagamento na Retirada";
				break;
				case "bankslip" :
					return "Boleto Bancário";
				break;
				default : 
					return $value;
				break;
			}
        }
        
        public static function _translate_princing_role($id)
		{
            $pricing = PricingRole::find($id);
			return ["id" => $pricing->id, "description" => $pricing->name];
		}


	}
