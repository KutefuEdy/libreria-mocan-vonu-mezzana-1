<?php
namespace App\Services;

use App\Core\Helpers;

class QrCodeService
{
  public static function generateBookQrByCode(string $code): string
  {
    $url = Helpers::url('/libri/qr/' . urlencode($code));
    $basePath = dirname(__DIR__, 2);
    $dir = $basePath . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrcodes';
    if (!is_dir($dir)) {
      mkdir($dir, 0775, true);
    }
    $safe = preg_replace('/[^A-Za-z0-9_\-]/', '_', $code);
    $filename = 'libro_' . $safe . '.png';
    $filePath = $dir . DIRECTORY_SEPARATOR . $filename;
    if (!class_exists('\Endroid\QrCode\Builder\Builder')) {
      $autoload = $basePath . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
      if (is_file($autoload)) {
        require_once $autoload;
      }
    }
    $saved = false;
    if (class_exists('\Endroid\QrCode\Builder\Builder') && class_exists('\Endroid\QrCode\Writer\PngWriter')) {
      $builderClass = '\Endroid\QrCode\Builder\Builder';
      $writerClass = '\Endroid\QrCode\Writer\PngWriter';
      try {
        $result = $builderClass::create()
          ->writer(new $writerClass())
          ->data($url)
          ->size(300)
          ->margin(10)
          ->build();
        $result->saveToFile($filePath);
        $saved = true;
      } catch (\Throwable $e) {
        $saved = false;
      }
    }
    if (!$saved) {
      if (function_exists('imagecreatetruecolor')) {
        $img = imagecreatetruecolor(300, 300);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        imagefilledrectangle($img, 0, 0, 300, 300, $white);
        imagerectangle($img, 0, 0, 299, 299, $black);
        imagestring($img, 5, 10, 10, 'QR non disponibile', $black);
        imagestring($img, 3, 10, 40, 'Installa endroid/qr-code', $black);
        imagestring($img, 3, 10, 70, $url, $black);
        imagepng($img, $filePath);
        imagedestroy($img);
      } else {
        $pngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAASwAAAEsCAYAAAB2VQwHAAAACXBIWXMAAAsSAAALEgHS3X78AAABlUlEQVR4nO3RMQEAAAgDoP1/6VdQYQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAPwZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAP8ZQO4AAQAAAAAAAP8EwP8BAAAAAP//AwAAAAAAAP8BAAAAAAAA4QAAAHwAAA==';
        $data = base64_decode($pngBase64);
        file_put_contents($filePath, $data);
      }
    }
    return 'public/qrcodes/' . $filename;
  }
}
