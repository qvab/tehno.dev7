<?php

namespace MIWR;
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);
define('URL_DESKTOP', 'http://serialsdate.ru');
define('PAGE_URL', $_SERVER['REQUEST_URI']);

class SystemCache
{

  public $pathCache = ROOT_PATH.'/wp-content/cache/miwr_cache';
  public $pathTotalCache = ROOT_PATH.'/wp-content/cache/page_enhanced/tehno.guru';
  private $typeObject;
  private $orgPath;
  private $modulePath;
  public $thisCreated = true;

  // Вывод отладочной информации
  private $textDebug = '';

  function __construct()
  {
    if (!is_dir($this->pathCache)) {
     mkdir($this->pathCache, 0777);
    }
  }

  public function debug()
  {
    return $this->textDebug;
  }

  // Получение кэша, или создание если его нету
  public function get($module, $file, $url = PAGE_URL, $data = false)
  {
    $this->modulePath = $module["path"];
    $this->textDebug .= 'Путь оригинального файла: '.$file.'<br />';
    $fileCache = $this->createdFilePath($module, $file, $url);
    if ($fileCache) {
      // Проверка наличия файла кэща
      if (file_exists($fileCache)) {
        $this->textDebug .= 'Передаем управление выводу: '.$fileCache.'<br />';
        return $fileCache;
      } else {
        $this->textDebug .= 'Такого файла нету: '.$fileCache.' - передаем управление функции Save<br />';
        return $this->save($fileCache, $file, $data);
      }
    }
    return false;
  }

  public function update($module, $file, $url = PAGE_URL, $data = false)
  {
    $fileCache = $this->createdFilePath($module, $file, $url);
    return $this->save($fileCache, $file, $data);
  }

  // Получение кэша, или создание если его нету (Возврат данных)
  public function response($module, $file, $url = PAGE_URL, $data = false)
  {
    $this->textDebug .= 'Путь оригинального файла: '.$file.'<br />';
    $fileCache = $this->createdFilePath($module, $file, $url);
    if ($fileCache) {
      // Проверка наличия файла кэща
      if (file_exists($fileCache)) {
        $this->textDebug .= 'Передаем управление выводу: '.$fileCache.'<br />';
        return $fileCache;
      } else {
        $this->textDebug .= 'Такого файла нету: '.$fileCache.' - передаем управление функции Save<br />';
        return $this->save($fileCache, $file, $data);
      }
    }
    return false;
  }

  // Проверка существования кэша
  public function check($module, $file, $url = PAGE_URL)
  {
    $fileCache = $this->createdFilePath($module, $file, $url);
    if ($fileCache) {
      if (file_exists($fileCache)) {
        return $fileCache;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  // Удаление файла кэша
  public function delete($module, $file, $url = PAGE_URL)
  {
    $fileCache = $this->createdFilePath($module, $file, $url);
    if ($fileCache) {
      // Проверка наличия файла кэща
      if (file_exists($fileCache)) {
        unlink($fileCache);
      }
    }
  }

  // Хеширование URL file
  private function hashUrl($str, $type = 'sha256', $fileExt = 'php')
  {
    return hash($type, $str).'.'.$fileExt;
  }

  // Проверка существования папки и ее создание
  private function createdDir($path)
  {
    if (!file_exists($path)) {
      if (mkdir($path, 0777, true)) {
        $this->textDebug .= 'Cоздания директории: '.$path.'<br />';
        return true;
      } else {
        $this->textDebug .= 'Ошибка создания директории: '.$path.'<br />';
        return false;
      }
    } else {
      $this->textDebug .= 'Директория уже создана: '.$path.'<br />';
      return true;
    }
  }

  // Определение пути файла
  private function createdFilePath($module, $file, $url = false)
  {
    // Если файл пренадлежит конкретному модулю
    if ($module['path']) {
      $path = $this->pathCache.$module['path'].'/';
      // Если файл без упровляющего модуля
    } else {
      $path = $this->pathCache.'__files.static/';
    }
    // Создание дириктории если ее нету
    if ($this->createdDir($path)) {
      // Учитывать ли URL страницы для шифрования
      if ($url) {
        $path .= $this->hashUrl($path.$file.$url);
        $this->textDebug .= 'Путь файла с URL: '.$path.'<br />';
      } else {
        $path .= $this->hashUrl($path.$path.$file);
        $this->textDebug .= 'Путь файла без учета URL: '.$path.'<br />';
      }
      return $path;
    } else {
      return false;
    }
  }

  // Сохранение файла фэша
  private function save($fileCache, $file, $data = false)
  {
    $__FILESAVE = $file;
    include_once $_SERVER["DOCUMENT_ROOT"]."/static/system.cache/class.proxy.php";
    $obProxy = new proxy();
    $arProxy = $obProxy->getRequest("https://".$_SERVER["SERVER_NAME"]."/".$__FILESAVE);
    if ($arProxy["curl"]["code"] == 200) {
      $this->textDebug .= 'Создаем файл кэша '.$fileCache.' для файла: '.$__FILESAVE.'<br />';
      if ($data) {
        extract($data, EXTR_PREFIX_SAME, "view");
      }
      ob_start();
      echo $arProxy["curl"]["response"];
      $content = ob_get_contents();
      ob_end_clean();
      if ($data) {
        foreach ($data as $key => $val) {
          unset($$key);
        }
      }
      $content = str_replace(
        [
          "[PHPCODE]",
          "[/PHPCODE]",
          "  ",
          "<!--[MW_CACHE]blkLastEpisodes[/MW_CAHCE]-->"
        ],
        [
          "<?php",
          "?>",
          " ",
          '<?php include_once $blkEpisode; ?>'
        ], $content);
      $content = str_replace(["\r\n", "\n\n", "\r\r", "\n", "\t", "\r"], "", $content);
      $content = str_replace("  ", " ", $content);
      $source = fopen($fileCache, "w");
      fwrite($source, $content);
      return $fileCache;
    }
    return false;
  }

}