<?php
namespace App\Helpers;

class Random
{
	static public function character($count) {
        $randomChar = "";
        $charBase = explode(" ", "a b c d e f g h i j k l m n o p q r s t u v w x y z A B C D E F G H I J K L M N O P Q R S T U V W X Y Z 0 1 2 3 4 5 6 7 8 9");

        for ($i = 0; $i < $count; $i++) {
            $randomChar = $randomChar . $charBase[rand(0, count($charBase) - 1)];
        }
        return $randomChar;
    }
    static public function number($count)
    {
        $randomNumb = "";
        $numbBase = explode(" ", "0 1 2 3 4 5 6 7 8 9");

        for ($i = 0; $i < $count; $i++) {
            $randomNumb = $randomNumb . $numbBase[rand(0, count($numbBase) - 1)];
        }
        return $randomNumb;
    }
}