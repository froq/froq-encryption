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

namespace froq\encrypting\oneway;

/**
 * Oneway.
 * @package froq\encrypting\oneway
 * @object  froq\encrypting\oneway\Oneway
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   1.0
 */
abstract class Oneway
{
    /**
     * Options.
     * @var array<string, any|null>
     */
    protected array $options = [];

    /**
     * Constructor.
     * @param array<string, any|null>|null $options
     */
    public function __construct(array $options = null)
    {
        $this->options = array_merge($this->options, $options ?? []);
    }

    /**
     * Get option.
     * @return array<string, any|null>
     */
    public final function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Hash make.
     * @param  string     $input
     * @param  array|null $options
     * @return ?string
     * @since  4.5
     */
    public static final function make(string $input, array $options = null): ?string
    {
        return (new static($options))->hash($input);
    }

    /**
     * Hash verify.
     * @param  string $input
     * @param  string $inputHash
     * @return bool
     * @since  4.5
     */
    public static final function validate(string $input, string $inputHash): bool
    {
        return (new static())->verify($input, $inputHash);
    }

    /**
     * Hash.
     * @param  string $input
     * @return ?string
     */
    public abstract function hash(string $input): ?string;

    /**
     * Verify.
     * @param  string $input
     * @param  string $inputHash
     * @return bool
     */
    public abstract function verify(string $input, string $inputHash): bool;
}
