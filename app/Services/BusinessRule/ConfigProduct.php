<?php

namespace App\Services\BusinessRule;

use App\Models\Sku;

class ConfigProduct
{
    public function getOrderMinPriceCombination($options, $target_qty)
    {
        usort($options, function ($x, $y) {
            return intval($x['amount']) >= intval($y['amount']);
        });
        $target_qty = intval($target_qty);
        $min_qty    = (int) $options[0]["amount"];
        if ($min_qty > $target_qty) return ["other_options" => [$min_qty]];
        $options = $this->removeAndProccessSmallerOrderPriceOption($options, $target_qty, $min_qty);
        $combinations = $this->getOrderPriceCombinations($options, $target_qty, $min_qty);
        if (@$combinations["other_options"]) return $combinations;
        $combinations = $this->proccessPrices($combinations);
        usort($combinations, function ($x, $y) {
            return $x['total_price'] >= $y['total_price'];
        });
        return $combinations[0];
    }

    private function proccessPrices($combinations)
    {
        for ($i = 0; $i < count($combinations); $i++) {
            $total = 0;
            for ($y = 0; $y < count($combinations[$i]["combination"]["skus"]); $y++) {
                $add_valorem = $combinations[$i]["combination"]["skus"][$y]["sku"]["reseller_price"] ? $combinations[$i]["combination"]["skus"][$y]["sku"]["reseller_price"] : $combinations[$i]["combination"]["skus"][$y]["sku"]["price"];
                $total += $combinations[$i]["combination"]["skus"][$y]["qty"] * $add_valorem;
            }
            $combinations[$i]["total_price"] = $total;
        }
        return $combinations;
    }

    private function recursive_minus($combinations, $options, $rest, $target_qty, $stop = false)
    {
        foreach ($options as $option) {
            $aux   = [];
            $qty   = 0;
            $price = 0;
            $amount = intval($option["amount"]);
            while ($qty == 0 || $rest >= $amount) {
                if ($rest < $amount) break;
                $qty++;
                $price += floatval($option["price"]);
                $rest -= $amount;
                if ($rest != 0 && $rest < $amount) break;
            }
            $option["qty"] = $qty;
            if ($qty > 0) $aux["skus"][] = $option;
            if ($rest == 0) {
                $combinations[] = [
                    "combination"  => $aux,
                ];
            } else {
                $new_options = array_filter($options, function ($x) use ($rest) {
                    return intval($x["amount"]) <= $rest;
                });
                $add_combinations = $this->recursive_minus([], $new_options, $rest, $rest, $rest == 200);
                foreach ($add_combinations as $_comb) {
                    $_aux["skus"] = $aux["skus"];
                    foreach (@$_comb["combination"]["skus"] as $_sku) {
                        @$_aux["skus"][] = $_sku;
                    }
                    $combinations[] = [
                        "combination"  => [
                            "skus" => $_aux["skus"],
                        ]
                    ];
                }
            }
            $aux = [];
            $rest        = $target_qty;
        }
        return $combinations;
    }

    private function getOrderPriceCombinations($options, $target_qty, $min_qty)
    {
        $combinations = [];
        $rest  = $target_qty;
        $combinations = $this->recursive_minus($combinations, $options, $target_qty, $target_qty);

        return $this->getOtherCombinationOrderPrice($combinations, $target_qty, ($rest < 0 ? $rest * -1 : $rest), $min_qty, $options);
    }

    private function getOtherCombinationOrderPrice($combinations, $target_qty, $rest, $min_qty, $options)
    {
        if (count($combinations) > 0) return $combinations;
        foreach ($options as $option) {
            $amount = intval($option["amount"]);
            if ($amount < $target_qty) {
                $min = intval($target_qty / $amount) * $amount;
                $max = $min + intval($options[count($options) - 1]["amount"]);
                return ["other_options" => [$min, $max]];
            }
        }
    }

    private function removeAndProccessSmallerOrderPriceOption($options, $target_qty)
    {
        $new_options = [];
        foreach ($options as $option) {
            if (intval($option["amount"]) <= intval($target_qty)) {
                $sku = @$option["sku"] ? (array) $option["sku"] : Sku::findOrFail($option["sku_id"]);
                $new_options[] = [
                    "rule"   => $option["rule"],
                    "amount" => $option["amount"],
                    "sku"    => $option["sku"],
                    "price"  => $sku["reseller_price"],
                    "fvalue" => $option["fvalue"]
                ];
            }
        }
        return $new_options;
    }
}
