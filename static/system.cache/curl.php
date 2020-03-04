<?php

namespace MIWR;
$q = "/best-cordless-hot-glue-guns/";
//$q = "https://tehno.guru/best-angle-grinders/";
$q = $_GET["q"];

require_once $_SERVER["DOCUMENT_ROOT"] . "/static/system.cache/index.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/static/system.cache/spriteParse.php";
$MW_CACHE = new SystemCache;
$sprite = new UQGenerateSprite;
$arURL = parse_url($q);
$arPath = explode("/", $arURL["path"]);
$clearPath = [];
$sPath = '';
foreach ($arPath as $val) {
  if (!empty($val)) {
    $clearPath[] = $val;
    $sPath .= $val . "/";
    if (!is_dir($MW_CACHE->pathCache . "/" . $sPath)) {
      mkdir($MW_CACHE->pathCache . "/" . $sPath, 0777);
    }
  }
}
$clearPath = implode("/", $clearPath);
$sTotalCacheFile = empty($clearPath) ? "/_index_ssl.html" : "/" . $clearPath . "/_index_ssl.html";


if (!file_exists($MW_CACHE->pathCache . "/" . $sTotalCacheFile)) {
  if (file_exists($MW_CACHE->pathTotalCache . $sTotalCacheFile)) {
    ob_start();
    include $MW_CACHE->pathTotalCache . $sTotalCacheFile;
    $content = ob_get_contents();
    ob_end_clean();
    $sprite->init($content);
    $afterContentSprite = $sprite->content;
    $fn = fopen($MW_CACHE->pathCache . "/" . $sTotalCacheFile, "w");
    fwrite($fn, $afterContentSprite);
    fclose($fn);
    touch($MW_CACHE->pathCache . "/" . $sTotalCacheFile, filemtime($MW_CACHE->pathTotalCache . $sTotalCacheFile));
    echo $afterContentSprite;
  } else {
    echo "total cache not file";
  }
} else {
  if (filemtime($MW_CACHE->pathTotalCache . $sTotalCacheFile) != filemtime($MW_CACHE->pathCache . "/" . $sTotalCacheFile)) {
    if (file_exists($MW_CACHE->pathTotalCache . $sTotalCacheFile)) {
      ob_start();
      include $MW_CACHE->pathTotalCache . $sTotalCacheFile;
      $content = ob_get_contents();
      ob_end_clean();
      $sprite->init($content);
      $afterContentSprite = $sprite->content;
      $fn = fopen($MW_CACHE->pathCache . "/" . $sTotalCacheFile, "w");
      fwrite($fn, $afterContentSprite);
      fclose($fn);
      touch($MW_CACHE->pathCache . "/" . $sTotalCacheFile, filemtime($MW_CACHE->pathTotalCache . $sTotalCacheFile));
      echo $afterContentSprite;
    } else {
      echo "total cache not file";
    }
  } else {
    include_once $MW_CACHE->pathCache . "/" . $sTotalCacheFile;
  }
}