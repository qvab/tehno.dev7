<?php

class generateCaptcha
{

  private
    $im,
    $colors,
    $fonts,
    $size = [
    "countChars" => 6,
    "w" => 150,
    "h" => 35,
    "font-size" => 19,
    "x" => 10,
    "y" => 25
  ];

  function __construct()
  {
    session_start();
    $this->fonts = [
      "Candara" => $_SERVER["DOCUMENT_ROOT"] . "/wp-includes/mishanin/captcha/Candara.ttf",
      "CandaraBold" => $_SERVER["DOCUMENT_ROOT"] . "/wp-includes/mishanin/captcha/Candarab.ttf"
    ];
    $this->generateImg();
    $this->generateText();
  }

  private function generateText()
  {
    $arChars = ["Q", "W", "E", "R", "T", "Y", "U", "I", "P", "A", "S", "D", "F", "G", "H", "J", "K", "L", "Z", "X", "C", "V", "B", "N", "M", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
    $iEnd = count($arChars);
    $sText = "";
    for ($i = 0; $i < $this->size["countChars"]; $i++) {
      $iKey = rand(0, $iEnd);
      $sText .= $arChars[$iKey];
    }
    imagefttext($this->im,
      $this->size["font-size"],
      0,
      $this->size["x"],
      $this->size["y"],
      $this->colors["black"],
      $this->fonts["CandaraBold"],
      $sText);
    $_SESSION["captcha_mishanin"] = mb_strtolower($sText);
  }


  private function generateImg()
  {
    $this->im = imagecreatetruecolor($this->size["w"], $this->size["h"]);
    $this->colors["white"] = imagecolorallocate($this->im, 240, 240, 240);
    $this->colors["black"] = imagecolorallocate($this->im, 0, 0, 0);
    imagefilledrectangle($this->im, 0, 0, $this->size["w"], $this->size["h"], $this->colors["white"]);
  }

  public function get()
  {
    header("Content-Type: image/png");
    imagepng($this->im);
    imagedestroy($this->im);
  }

}

$obSelf = new generateCaptcha();
$obSelf->get();