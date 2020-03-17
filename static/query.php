<?php


function vd($data) {
  echo "<pre>";
  var_dump($data);
  echo "</pre>";
}

$db = mysqli_connect("localhost", "tehnoguru", 'Hvecj1Sz3$P@7]2p', "tehnoguru");

/*
$res = mysqli_query($db, "SELECT meta_id, meta_value FROM wp_postmeta WHERE meta_value LIKE '%[section label%'");
$arForReplace = [];

while ($response = mysqli_fetch_assoc($res)) {
  $newData = preg_replace("|\[section.*?\]|", "", $response["meta_value"]);
  $arForReplace[$response["meta_id"]] = mysqli_escape_string($db, $newData);
  $bUpdate = mysqli_query($db, "UPDATE wp_postmeta SET meta_value = '{$arForReplace[$response["meta_id"]]}' WHERE meta_id = {$response["meta_id"]}");
  vd([$response["meta_id"] => $bUpdate, "error" => empty($bUpdate) ? mysqli_error($db) : false]);
}
*/



$res = mysqli_query($db, "SELECT ID, post_content FROM wp_posts WHERE post_content LIKE '%[section label%'");
$arForReplace = [];

while ($response = mysqli_fetch_assoc($res)) {
  $newData = preg_replace("|\[section.*?\]|", "", $response["post_content"]);
  $arForReplace[$response["ID"]] = mysqli_escape_string($db, $newData);
  $bUpdate = mysqli_query($db, "UPDATE wp_posts SET post_content = '{$arForReplace[$response["ID"]]}' WHERE ID = {$response["ID"]}");
  vd([$response["ID"] => $bUpdate, "error" => empty($bUpdate) ? mysqli_error($db) : false]);
}
