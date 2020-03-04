<?php

function resizeImage($sPath, $newWidth, $newHeight)
{
  $arURL = parse_url($sPath);
  $arURL = explode("/", $arURL["path"]);
  $sURL = $newWidth."x".$newHeight."_".end($arURL);
  if (!file_exists($_SERVER["DOCUMENT_ROOT"] . "/wp-content/cache/sprite_cache/resize/" . $sURL)) {
    $arGetData = getimagesize($_SERVER["DOCUMENT_ROOT"] . $sPath);
    $img = false;
    switch ($arGetData["mime"]) {
      case "image/jpeg":
        $img = imagecreatefromjpeg($_SERVER["DOCUMENT_ROOT"] . $sPath);
        break;
      case "image/png":
        $img = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"] . $sPath);
        imagealphablending($img, false);
        imagesavealpha($img, true);
        break;
      case "image/gif":
        $img = imagecreatefromgif($_SERVER["DOCUMENT_ROOT"] . $sPath);
        break;
    }
    if (empty($img)) {
      echo "fatal error";
      exit();
    }
    $place = imagecreatetruecolor($newWidth, $newHeight) or die("Невозможно создать поток изображения");
    imagealphablending($place, false);
    imagesavealpha($place, true);
    $rBackground = imagecolorallocatealpha($place, 0, 0, 0, 50);
    imagefill($place, 0, 0, $rBackground);
    imagecopyresampled($place, $img, 0, 0, 0, 0, $newWidth, $newHeight, $arGetData[0], $arGetData[1]);
    imagepng($place, $_SERVER["DOCUMENT_ROOT"] . "/wp-content/cache/sprite_cache/resize/" . $sURL);
    imagedestroy($place);
  }
  return "/wp-content/cache/sprite_cache/resize/" . $sURL;
}

$newFile = resizeImage("/wp-content/uploads/2020/02/luchshie-detektory-provodki-300x225.jpg", 64, 64);