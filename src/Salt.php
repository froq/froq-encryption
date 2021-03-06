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

namespace froq\encrypting;

use froq\encrypting\{Base, EncryptingException};

/**
 * Salt.
 * @package froq\encrypting
 * @object  froq\encrypting\Salt
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   3.0
 * @static
 */
final class Salt
{
    /**
     * Length.
     * @const int
     */
    public const LENGTH = 128;

    /**
     * Bits-per-char.
     * @const int
     */
    public const BITS_PER_CHAR = 6;

    /**
     * Generate.
     * @param  int|null $length      Output length.
     * @param  int|null $bitsPerChar 4=base16 (hex), 5=base36, 6=base62.
     * @return string
     * @throws froq\encrypting\EncryptingException.
     */
    public static function generate(int $length = null, int $bitsPerChar = null): string
    {
        $len = $length ?? self::LENGTH;
        $bpc = $bitsPerChar ?? self::BITS_PER_CHAR;

        if ($len < 2) {
            throw new EncryptingException('Invalid length value "%s" given, length must be '.
                'greater than 1', [$len]);
        } elseif ($bpc < 4 || $bpc > 6) {
            throw new EncryptingException('Invalid bits-per-char value "%s" given, valids are: '.
                '4, 5, 6', [$bpc]);
        }

        $bytes = random_bytes((int) ceil($len * $bpc / 8));

        $chars = ($bpc == 6) ? Base::BASE_62_CHARS : (
            ($bpc == 5) ? Base::BASE_36_CHARS : Base::BASE_16_CHARS);
        $charsLength = strlen($chars);

        // Original source https://github.com/php/php-src/blob/master/ext/session/session.c#L267,#L326.
        $p = 0; $q = strlen($bytes);
        $w = 0; $have = 0; $mask = (1 << $bpc) - 1;

        $out = '';

        while ($len--) {
            if ($have < $bpc) {
                if ($p < $q) {
                    $byte = ord($bytes[$p++]);
                    $w |= ($byte << $have);
                    $have += 8;
                } else {
                    break;
                }
            }

            $i = $w & $mask;

            // Fix up index picking a random index.
            if ($i > $charsLength - 1) {
                $i = mt_rand(0, $charsLength - 1);
            }

            $out .= $chars[$i];

            $w >>= $bpc;
            $have -= $bpc;
        }

        return $out;
    }
}
