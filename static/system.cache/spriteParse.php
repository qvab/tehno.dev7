<?php

namespace MIWR;
function vd($data, $bPrint = false)
{
  echo "<pre>";
  if (!empty($bPrint)) {
    var_dump($data);
  } else {
    print_r($data);
  }
  echo "</pre>";
}


class UQGenerateSprite
{

  public $content;
  private $pathSave;
  private $pathRelativeSprite;
  public $arPageFiles = [];
  private $iPlaceWidth = 0;
  private $iPlaceHeight = 0;
  private $iPlaceWidthAlpha = 0;
  private $iPlaceHeightAlpha = 0;

  private $rImage, $rImageAlpha;
  private $sHashNameSprite = "background_";
  private $sHashNameSpriteAlpha = "alpha_";
  private $sFormatFile = "jpg";
  private $isFile = false;
  private $arElements = [];
  private $arElementsID = [];
  private $sHost;
  private $maxImgWidth = 750;
  private $maxImgHeight = 720;
  private $iPaddingX = 0;
  private $iPaddingY = 0;
  private $stop = false;


  public function init($content)
  {
    $this->pathSave = $_SERVER["DOCUMENT_ROOT"] . '/wp-content/cache/sprite_cache';
    $this->pathRelativeSprite = '/wp-content/cache/sprite_cache';
    if (!is_dir($this->pathSave)) {
      if (!mkdir($this->pathSave, 0777)) {
        echo "error: created dir";
        exit();
      };
    }
    $this->content = $content;
    $this->parse();
    if (!$this->stop) {
      if (!$this->isFile) {
        $this->createdPlace();
        $this->insertImages();
      }
      $this->replaceContent();
    }
  }

  private function createdFromImg($img)
  {
    $im = false;
    switch ($img["type"]) {
      case "image/jpeg":
        $im = imagecreatefromjpeg($img["root_path"]);
        break;
      case "image/png":
        $im = imagecreatefrompng($img["root_path"]);
        imagealphablending($im, false);
        imagesavealpha($im, true);
        break;
      case "image/gif":
        $im = imagecreatefromgif($img["root_path"]);
        break;
    }
    if (empty($im)) {
      echo "fatal error";
      exit();
    }
    return $im;
  }


  public function filesJSON()
  {
    return json_encode($this->arPageFiles, true);
  }

  private function replaceContent()
  {
/*
    vd($this->iPlaceHeightAlpha);
    vd($this->iPlaceHeight);
*/
    foreach ($this->arPageFiles as $k => $v) {
      $this->arElements[] = $v["replace_filed"];
      $this->arElementsID[] = 'data-img-sprite="true" data-src="' . $v["relative_path"] . '" data-img-url="' . $this->pathRelativeSprite . "/" . $v["resize"]["hash"]
        . '" data-sprite-x="0" data-sprite-y="' . $v["shiftTop"]
        . '" data-sprite-width="' . $v["width"]
        . '" data-sprite-height="' . $v["height"]
        . '" data-sprite-host="' . $this->sHost
        . '" data-sprite-type="' . ($v["height"] > 100 ? "background" : "alpha")
        . '" data-sprite-space-w="' . ($v["height"] > 100 ? $this->iPlaceWidth : $this->iPlaceWidthAlpha)
        . '" data-sprite-space-h="' . ($v["height"] > 100 ? $this->iPlaceHeight : $this->iPlaceHeightAlpha)
        . '"' . ($v["keyMatch"] == 0 ? "data-sprite-type='0' src='/static/loader-trans.gif'" : "data-sprite-type='1' style='background-image(/static/loader-trans.gif); background-size: contain;'");
    }
    $this->content = str_replace($this->arElements, $this->arElementsID, $this->content);
  }


  private function pregMatches()
  {
    $this->content = preg_replace('#srcset=\".*?\"#', "", $this->content);
    preg_match_all('|<script[^<]*>([^<]*AdvManager[^<]*)</script>|s', $this->content, $arMatchScripts);
    $this->content = preg_replace('|<script[^<]*>([^<]*AdvManager[^<]*)</script>|s', "", $this->content);
    $sAllScripts = "<script class='new_script'>setTimeout(function() { " . implode("\n", $arMatchScripts[1]) . "}, 7000);</script>";
    $this->content = preg_replace('|</body>|', $sAllScripts . "</body>", $this->content);
    preg_match_all('#(\<img.*?(src\=(\"|\')(.*?)(\"|\')).*?\>)#', $this->content, $matches_1);
    preg_match_all('#(\<.*?(style\=(\"|\')(background|background\-image)\:url\((.*?)\).*?\;(\"|\')).*?\>)#', $this->content, $matches_2);
    return [
      0 => [
        "matches" => $matches_1,
        "key_url" => 4,
        "key_field" => 2
      ],
      1 => [
        "matches" => $matches_2,
        "key_url" => 5,
        "key_field" => 2
      ],
    ];
  }

  private function parse()
  {
    $arMatches = $this->pregMatches();
    foreach ($arMatches as $keyMatch => $arMatchVal) {
      if (!empty($arMatches[$keyMatch]["matches"])) {
        foreach ($arMatches[$keyMatch]["matches"][0] as $k => $matches) {
          $keyUrl = &$arMatches[$keyMatch]["key_url"];
          $keyField = &$arMatches[$keyMatch]["key_field"];
          $sUrl = &$arMatches[$keyMatch]["matches"][$keyUrl][$k];
          $sField = &$arMatches[$keyMatch]["matches"][$keyField][$k];
          $arURL = parse_url($sUrl);

          $arDataFile = getimagesize($_SERVER["DOCUMENT_ROOT"] . $arURL["path"]);

          if ($arDataFile && ($arDataFile[1] > 3 && $arDataFile[0] > 3)) {
            if (!$this->sHost) {
              $this->sHost = $arURL["host"];
            }
            $this->arPageFiles[] = [
              "keyMatch" => $keyMatch,
              "replace_filed" => $sField,
              "relative_path" => $arURL["path"],
              "root_path" => $_SERVER["DOCUMENT_ROOT"] . $arURL["path"],
              "width" => $arDataFile[0],
              "height" => $arDataFile[1],
              "type" => $arDataFile["mime"],
              "resize" => false
            ];
            if ($arDataFile[0] < 100 || $arDataFile[1] < 100) {
              $this->sHashNameSprite .= $arURL["path"];
            } else {
              $this->sHashNameSpriteAlpha .= $arURL["path"];
            }
          }
        }
        // Выполняем определние нужен ли ресайзинг изображений и там же получаем размеры полотна
      }
    }

    $this->sHashNameSprite = "back_".str_replace("/", "_", $_GET["q"]) . ".jpg";
    $this->sHashNameSpriteAlpha = "alpha_".str_replace("/", "_", $_GET["q"]) . ".png";// . $this->sFormatFile;
    if (!empty($this->arPageFiles)) {
      if (
        file_exists($this->pathSave . "/" . $this->sHashNameSprite) ||
        file_exists($this->pathSave . "/" . $this->sHashNameSpriteAlpha)
      ) {
        $this->isFile = true;
      }
      $this->testResizeImage();
    } else {
      $this->stop = true;
    }
  }

  private function createdPlace()
  {
    // Создаем основное полотно
    $this->rImage = imagecreatetruecolor($this->iPlaceWidth, $this->iPlaceHeight) or die("Невозможно создать поток изображения");
    $rBackground = imagecolorallocatealpha($this->rImage, 255, 255, 255, 127);
    imagesavealpha($this->rImage, true);
    imagefill($this->rImage, 0, 0, $rBackground);


    // Создаем основное полотно alpha
    $this->rImageAlpha = imagecreatetruecolor($this->iPlaceWidthAlpha, $this->iPlaceHeightAlpha) or die("Невозможно создать поток изображения");
    $rBackground = imagecolorallocatealpha($this->rImageAlpha, 0, 0, 0, 127);
    imagesavealpha($this->rImageAlpha, true);
    imagefill($this->rImageAlpha, 0, 0, $rBackground);


  }

  private function insertImages()
  {
    foreach ($this->arPageFiles as $k => $v) {
      //vd($v);
      if (!empty($v["resize"])) {
        $img = $this->resizeImage($v);
      } else {
        $img = $this->createdFromImg($v);
      }

      if ($v["height"] < 100) {
        imagecopy($this->rImageAlpha, $img, 0, $v["shiftTop"], 0, 0, $v["width"], $v["height"]);
      } else {
        imagecopy($this->rImage, $img, 0, $v["shiftTop"], 0, 0, $v["width"], $v["height"]);
      }
      $this->arPageFiles[$k]["left"] = 0;
      imagedestroy($img);
    }
    /*header("Content-type: image/jpg");
    imagepng($this->rImageAlpha);
    imagejpeg($this->rImage);*/
    imagejpeg($this->rImage, $this->pathSave . "/" . $this->sHashNameSprite, 60);
    imagepng($this->rImageAlpha, $this->pathSave . "/" . $this->sHashNameSpriteAlpha, 9);
    imagedestroy($this->rImage);
    imagedestroy($this->rImageAlpha);
  }

  public function getCurrentSprite()
  {
    if (!empty($this->sHashNameSprite)) {
      echo '<b>NAME: ' . $this->sHashNameSprite . '<br /><img style="border: 1px #000 solid;" src="' . $this->pathRelativeSprite . "/" . $this->sHashNameSprite . '?v=' . time() . '" />';
    }
    echo "none";
  }


  public function clearCurrentSprite()
  {
    unlink($this->pathSave . "/" . $this->sHashNameSprite);
  }


  private function testResizeImage()
  {
    $iShiftTop = $iShiftTopAlpha = 0;
    $arWidths = [];
    $arWidthsAlpha = [];

    foreach ($this->arPageFiles as $key => $file) {
      $iPercentWidth = ($file["width"] / 100);                // 1/100 от ширины картники
      $iPercentHeight = ($file["height"] / 100);              // 1/100 от высоты картники
      $iLossPercent = 0;
      if ($file["width"] > $this->maxImgWidth) {
        $iLossPercent = (($file["width"] - $this->maxImgWidth) / $iPercentWidth);
      }
      $iNewWidthImg = round($iPercentWidth * (100 - $iLossPercent));
      $iNewHeightImg = round($iPercentHeight * (100 - $iLossPercent));
      $this->arPageFiles[$key]["width"] = $iNewWidthImg;
      $this->arPageFiles[$key]["height"] = $iNewHeightImg;

      if ($this->arPageFiles[$key]["height"] < 100) {
        $sHash = $this->sHashNameSpriteAlpha;
      } else {
        $sHash = $this->sHashNameSprite;
      }

      $this->arPageFiles[$key]["resize"] = [
        "x" => 0,
        "y" =>  $this->arPageFiles[$key]["height"] < 100 ? $this->iPlaceHeightAlpha : $this->iPlaceHeight,
        "newWidth" => $iNewWidthImg,
        "newHeight" => $iNewHeightImg,
        "realWidth" => $file["width"],
        "realHeight" => $file["height"],
        "hash" => $sHash
      ];

      if ($this->arPageFiles[$key]["height"] < 100) {
        $this->arPageFiles[$key]["shiftTop"] = $iShiftTopAlpha;
        $iShiftTopAlpha += $this->arPageFiles[$key]["height"];
        $arWidthsAlpha[] = $this->arPageFiles[$key]["width"];
        if ($this->iPlaceWidthAlpha < $this->arPageFiles[$key]["width"]) {
          $this->iPlaceWidthAlpha = $this->arPageFiles[$key]["width"];
        }
        $this->iPlaceHeightAlpha += $this->arPageFiles[$key]["height"];
      } else {
        $this->arPageFiles[$key]["shiftTop"] = $iShiftTop;
        $iShiftTop += $this->arPageFiles[$key]["height"];
        $arWidths[] = $this->arPageFiles[$key]["width"];
        if ($this->iPlaceWidth < $this->arPageFiles[$key]["width"]) {
          $this->iPlaceWidth = $this->arPageFiles[$key]["width"];
        }
        $this->iPlaceHeight += $this->arPageFiles[$key]["height"];
      }
    }
    $iMaxWidthInArray = max($arWidths);
    $iMaxWidthInArrayAlpha = max($arWidths);
    $this->iPlaceWidth = $iMaxWidthInArray < $this->iPlaceWidth ? $iMaxWidthInArray : $this->iPlaceWidth;
    $this->iPlaceWidthAlpha = $iMaxWidthInArrayAlpha < $this->iPlaceWidthAlpha ? $iMaxWidthInArrayAlpha : $this->iPlaceWidthAlpha;

  }

  private function resizeImage($thisImg)
  {
    $arResize = $thisImg["resize"];
    $img = $this->createdFromImg($thisImg);
    $place = imagecreatetruecolor($thisImg["width"], $thisImg["height"]) or die("Невозможно создать поток изображения");
    imagealphablending($place, false);
    imagesavealpha($place, true);
    $rBackground = imagecolorallocatealpha($place, 0, 0, 0, 50);
    imagefill($place, 0, 0, $rBackground);
    imagecopyresampled($place, $img, 0, 0, 0, 0, $arResize["newWidth"], $arResize["newHeight"], $arResize["realWidth"], $arResize["realHeight"]);
    return $place;
  }
}