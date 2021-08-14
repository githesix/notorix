<?php

namespace App\Maison;

use Illuminate\Http\Request; // Important pour accéder à Request!
use DB;
use Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Log;

/**
 * Unique identifier generator
 * 
 * Needs «composer require ircmaxell/random-lib»
 * 
 * Examples
 * 
 * Named-based UUID.
 * $v3uuid = UUID::v3('1546058f-5a25-4334-85ae-e68f2a44bbaf', 'SomeRandomString');
 * $v5uuid = UUID::v5('1546058f-5a25-4334-85ae-e68f2a44bbaf', 'SomeRandomString');
 * 
 * Pseudo-random UUID
 * $v4uuid = UUID::v4();
 */
class UUID {

  //private $alphasafe = "23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ";
  const ALPHASAFE = "23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ";
    
  public static function v3($namespace, $name) {
    if(!self::is_valid($namespace)) return false;

    // Get hexadecimal components of namespace
    $nhex = str_replace(array('-','{','}'), '', $namespace);

    // Binary Value
    $nstr = '';

    // Convert Namespace UUID to bits
    for($i = 0; $i < strlen($nhex); $i+=2) {
      $nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
    }

    // Calculate hash value
    $hash = md5($nstr . $name);

    return sprintf('%08s-%04s-%04x-%04x-%12s',

      // 32 bits for "time_low"
      substr($hash, 0, 8),

      // 16 bits for "time_mid"
      substr($hash, 8, 4),

      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 3
      (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,

      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

      // 48 bits for "node"
      substr($hash, 20, 12)
    );
  }

  public static function v4() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

      // 32 bits for "time_low"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff),

      // 16 bits for "time_mid"
      mt_rand(0, 0xffff),

      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 4
      mt_rand(0, 0x0fff) | 0x4000,

      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      mt_rand(0, 0x3fff) | 0x8000,

      // 48 bits for "node"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
  }

  public static function v5($namespace, $name) {
    if(!self::is_valid($namespace)) return false;

    // Get hexadecimal components of namespace
    $nhex = str_replace(array('-','{','}'), '', $namespace);

    // Binary Value
    $nstr = '';

    // Convert Namespace UUID to bits
    for($i = 0; $i < strlen($nhex); $i+=2) {
      $nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
    }

    // Calculate hash value
    $hash = sha1($nstr . $name);

    return sprintf('%08s-%04s-%04x-%04x-%12s',

      // 32 bits for "time_low"
      substr($hash, 0, 8),

      // 16 bits for "time_mid"
      substr($hash, 8, 4),

      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 5
      (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,

      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

      // 48 bits for "node"
      substr($hash, 20, 12)
    );
  }

  public static function is_valid($uuid) {
    return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
                      '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
  }
  
  /**
   * commStruc
   * Generate structured communication for SEPA (Belgium)
   *
   * @param  mixed $s
   * @return void
   */
  public static function commStruc($s = 0) {
      $d = sprintf("%010s", $s);
      $modulo = (bcmod($s, 97) == 0 ? 97 : bcmod($s, 97));
      return sprintf("%s/%s/%s%02d", substr($d, 0, 3), substr($d, 3, 4), substr($d, 7, 3), $modulo);
  }
    
  public static function uid16() {
    $alphasafe = self::ALPHASAFE;
      $factory = new \RandomLib\Factory();
      $generator = $factory->getMediumStrengthGenerator();
      return $generator->generateString(16, $alphasafe);
  }
  
  public static function uid8() {
    $alphasafe = self::ALPHASAFE;
      $factory = new \RandomLib\Factory();
      $generator = $factory->getMediumStrengthGenerator();
      return $generator->generateString(8, $alphasafe);
  }
    
  /**
   * gencomstrunique
   * Unused in Notorix
   * Method inherited from Thesix portal
   *
   * @return void
   */
  public static function gencomstrunique() {
      $f = new \RandomLib\Factory();
      $g = $f->getMediumStrengthGenerator();
      $s = $g->generateString(10, '0123456789');
      $modulo = (bcmod($s, 97) == 0 ? 97 : bcmod($s, 97));
      $d = sprintf("%s%02d", $s, $modulo);
      if (\App\Models\Paiement::where('comstruc', $d)->first()) {
          return self::gencomstrunique();
      } else {
          return "+++".sprintf("%s/%s/%s%02d", substr($d, 0, 3), substr($d, 3, 4), substr($d, 7, 3), $modulo)."+++";
      }
  }

}
