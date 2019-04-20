<?php
/**
 * MIT License <https://opensource.org/licenses/mit>
 *
 * Copyright (c) 2015 Kerem Güneş
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
declare(strict_types=1);

namespace froq\encryption;

use froq\encryption\oneway\Password;

/**
 * Encryption.
 * @package froq\encryption
 * @object  froq\encryption\Encryption
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   1.0
 */
final class Encryption
{
    /**
     * Chars.
     * @const string
     */
    public const CHARS_16 = '0123456789abcdef',
                 CHARS_36 = '0123456789abcdefghijklmnopqrstuvwxyz',
                 CHARS_62 = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * Nonce algos.
     * @var array
     */
    private static $nonceAlgos = [8 => 'fnv1a32', 16 => 'fnv1a64', 32 => 'md5', 40 => 'sha1',
        64 => 'sha256', 128 => 'sha512'];

    /**
     * Generate salt.
     * @param  int|null $length
     * @param  bool     $translate
     * @return string
     * @since  3.0
     */
    public static function generateSalt(int $length = null, bool $translate = false): string
    {
        return Salt::generate($length, $translate);
    }

    /**
     * Generate uuid.
     * @param  bool $simple
     * @param  bool $translate
     * @return string
     * @since  3.0
     */
    public static function generateUuid(bool $simple = true, bool $translate = false): string
    {
        return Uuid::generate($simple, $translate);
    }

    /**
     * Generate short uuid.
     * @param  int|null $base
     * @return string
     * @since  3.0
     */
    public static function generateShortUuid(int $base = null): string
    {
        return Uuid::generateShort($base);
    }

    /**
     * Generate long uuid.
     * @param  int|null $base
     * @return string
     * @since  3.6
     */
    public static function generateLongUuid(int $base = null): string
    {
        return Uuid::generateLong($base);
    }

    /**
     * Generate.
     * @param  int  $length
     * @param  bool $lettersOnly
     * @return string
     * @since  3.0
     */
    public static function generatePassword(int $length = 8, bool $lettersOnly = true): string
    {
        return Password::generate($length, $lettersOnly);
    }

    /**
     * Generate nonce.
     * @param  int  $length
     * @param  bool $randomBytes
     * @return string
     * @throws froq\encryption\EncryptionException
     * @since  3.0
     */
    public static function generateNonce(int $length = 40, bool $randomBytes = true): string
    {
        if (isset(self::$nonceAlgos[$length])) {
            $nonce = $randomBytes ? random_bytes(128) : self::generateSerial(); // use big number

            return hash(self::$nonceAlgos[$length], $nonce);
        }

        throw new EncryptionException(sprintf("Given length '{$length}' not implemented, only '%s' ".
            "are accepted", join(',', array_keys(self::$nonceAlgos))));
    }

    /**
     * Generate serial.
     * @return string A big number with 20-digit length long.
     * @since  3.7
     */
    public static function generateSerial(): string
    {
        [$time, $microtime] = (function () {
            $tmp = explode(' ', microtime());
            return [$tmp[1], substr($tmp[0], 2, 6)];
        })();

        return ''. $time . $microtime . random_int(1000, 9999);
    }
}
