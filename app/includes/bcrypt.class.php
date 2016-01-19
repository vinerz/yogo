<?php
/* ------------------------------------------------------------------------ *
 *    _  _ __   __  __                                                      *
 *   ( \/ )  \ / _)/  \    By Vinicius Tavares 2012                         *
 *    \  / () ) (/\ () )   Website: http://www.xenon-corporation.com/yogo   * 
 *   (__/ \__/ \__/\__/    Contact: vinerz@vinerz.net                       *
 *                                                                          *
 * ------------------------------------------------------------------------ *
 */
 
/* Special thanks to PHP Password Hashing Framework for this class base */

class Bcrypt {
  private static $rounds = 12;
  public function __construct($rounds = 12) {
    if(CRYPT_BLOWFISH != 1) {
      throw new Exception("bcrypt not supported in this installation. See http://php.net/crypt");
    }

    self::$rounds = $rounds;
  }

  public static function hash($input) {
    $hash = crypt($input, self::getSalt());

    if(strlen($hash) > 13)
      return $hash;

    return false;
  }

  public static function verify($input, $existingHash) {
    $hash = crypt($input, $existingHash);

    return $hash === $existingHash;
  }

  private static function getSalt() {
    $salt = sprintf('$2a$%02d$', self::$rounds);

    $bytes = self::getRandomBytes(16);

    $salt .= self::encodeBytes($bytes);

    return $salt;
  }

  private static $randomState;
  private static function getRandomBytes($count) {
    $bytes = '';

    if(function_exists('openssl_random_pseudo_bytes') &&
        (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')) { // OpenSSL slow on Win
      $bytes = openssl_random_pseudo_bytes($count);
    }

    if($bytes === '' && is_readable('/dev/urandom') &&
       ($hRand = @fopen('/dev/urandom', 'rb')) !== FALSE) {
      $bytes = fread($hRand, $count);
      fclose($hRand);
    }

    if(strlen($bytes) < $count) {
      $bytes = '';

      if(self::$randomState === null) {
        self::$randomState = microtime();
        if(function_exists('getmypid')) {
          self::$randomState .= getmypid();
        }
      }

      for($i = 0; $i < $count; $i += 16) {
        self::$randomState = md5(microtime() . self::$randomState);

        if (PHP_VERSION >= '5') {
          $bytes .= md5(self::$randomState, true);
        } else {
          $bytes .= pack('H*', md5(self::$randomState));
        }
      }

      $bytes = substr($bytes, 0, $count);
    }

    return $bytes;
  }

  private static function encodeBytes($input) {
    // The following is code from the PHP Password Hashing Framework
    $itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    $output = '';
    $i = 0;
    do {
      $c1 = ord($input[$i++]);
      $output .= $itoa64[$c1 >> 2];
      $c1 = ($c1 & 0x03) << 4;
      if ($i >= 16) {
        $output .= $itoa64[$c1];
        break;
      }

      $c2 = ord($input[$i++]);
      $c1 |= $c2 >> 4;
      $output .= $itoa64[$c1];
      $c1 = ($c2 & 0x0f) << 2;

      $c2 = ord($input[$i++]);
      $c1 |= $c2 >> 6;
      $output .= $itoa64[$c1];
      $output .= $itoa64[$c2 & 0x3f];
    } while (1);

    return $output;
  }
}