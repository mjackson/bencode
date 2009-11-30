<?php

/**
 * Contains a pair of recursive functions that implement the bencode data
 * encoding format.
 *
 * @author  Michael J. I. Jackson <michael@mjijackson.com>
 */
class Bencode
{

    /**
     * The version of the library.
     *
     * @var string
     */
    const VERSION = "1.0";

    /**
     * Bencodes the given data structure.
     *
     * @param   mixed
     * @return  string
     * @throws  Exception
     */
    public static function encode($value)
    {
        if (is_null($value)) {
            return "0:";
        }
        if (is_int($value)) {
            return "i" . $value . "e";
        }
        if (is_string($value)) {
            return strlen($value) . ":" . $value;
        }
        if (is_array($value)) {
            if (self::isDict($value)) {
                ksort($value, SORT_STRING);
                $buffer = "d";
                foreach ($value as $key => $v) {
                    $buffer .= self::encode(strval($key));
                    $buffer .= self::encode($v);
                }
                $buffer .= "e";
            } else {
                ksort($value, SORT_NUMERIC);
                $buffer = "l";
                foreach ($value as $v) {
                    $buffer .= self::encode($v);
                }
                $buffer .= "e";
            }
            return $buffer;
        }

        throw new Exception("Unable to encode data type: " . gettype($value));
    }

    /**
     * Decodes the given string. The second parameter is only used in recursion.
     *
     * @param   string
     * @param   int
     * @return  mixed
     * @throws  Exception
     */
    public static function decode($tokens, &$i=0)
    {
        if (is_string($tokens)) {
            $tokens = str_split($tokens);
        }

        switch ($tokens[$i]) {
        case "d":
            $dict = array();
            while (isset($tokens[++$i])) {
                if ($tokens[$i] == "e") {
                    return $dict;
                } else {
                    $key = self::decode($tokens, $i);
                    if (isset($tokens[++$i])) {
                        $dict[$key] = self::decode($tokens, $i);
                    } else {
                        throw new Exception("Dictionary key ($key) without a value at index $i");
                    }
                }
            }
            throw new Exception("Unterminated dictionary at index $i");

        case "l":
            $list = array();
            while (isset($tokens[++$i])) {
                if ($tokens[$i] == "e") {
                    return $list;
                } else {
                    $list[] = self::decode($tokens, $i);
                }
            }
            throw new Exception("Unterminated list at index $i");

        case "i":
            $buffer = '';
            while (isset($tokens[++$i])) {
                if ($tokens[$i] == "e") {
                    return intval($buffer);
                } elseif (ctype_digit($tokens[$i])) {
                    $buffer .= $tokens[$i];
                } else {
                    throw new Exception("Unexpected token while parsing integer at index $i: {$tokens[$i]}");
                }
            }
            throw new Exception("Unterminated integer at index $i");

        case ctype_digit($tokens[$i]):
            $length = $tokens[$i];
            while (isset($tokens[++$i])) {
                if ($tokens[$i] == ":") {
                    break;
                } elseif (ctype_digit($tokens[$i])) {
                    $length .= $tokens[$i];
                } else {
                    throw new Exception("Unexpected token while parsing string length at index $i: {$tokens[$i]}");
                }
            }
            $end = $i + intval($length);
            $buffer = '';
            while (isset($tokens[++$i])) {
                if ($i <= $end) {
                    $buffer .= $tokens[$i];
                    if ($i == $end) {
                        return $buffer;
                    }
                }
            }
            throw new Exception("Unterminated string at index $i");
        }

        throw new Exception("Unexpected token at index $i: {$tokens[$i]}");
    }

    /**
     * Returns true if the given array has any keys that are not integers.
     *
     * @param   array
     * @return  bool
     */
    protected static function isDict($array)
    {
        foreach ($array as $key => $value) {
            if (!is_int($key)) {
                return true;
            }
        }
        return false;
    }

}

/**
 * Shorthand for {@link Bencode::encode()}.
 *
 * @param   mixed
 * @return  string
 * @throws  Exception
 * @see     Bencode::encode()
 */
function bencode($value)
{
    return Bencode::encode($value);
}

/**
 * Shorthand for {@link Bencode::decode()}.
 *
 * @param   string
 * @return  mixed
 * @throws  Exception
 * @see     Bencode::decode()
 */
function bdecode($value)
{
    return Bencode::decode($value);
}
