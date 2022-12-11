<?php

namespace App\Util;

class StringUtil
{
    private const ENCODING = 'UTF-8';

    /**
     * @param array<int, string>|string $search
     * @param array<int, string>|string $replace
     * @param array<int, string>|string $subject
     * @param int $count
     * @return array<int, string>|string
     */
    public function mbStrReplace(array|string $search, array|string $replace, array|string $subject, int &$count = 0): array|string
    {
        if (!is_array($subject)) {
            // Normalize $search and $replace, so they are both arrays of the same length
            $searches = is_array($search) ? array_values($search) : array($search);
            $replacements = is_array($replace) ? array_values($replace) : array($replace);
            $replacements = array_pad($replacements, count($searches), '');
            foreach ($searches as $key => $search) {
                $parts = mb_split(preg_quote($search), $subject);
                $count += count($parts) - 1;
                $subject = implode($replacements[$key], $parts);
            }
        } else {
            // Call mbStrReplace for each subject in array, recursively
            foreach ($subject as $key => $value) {
                $subject[$key] = $this->mbStrReplace($search, $replace, $value, $count);
            }
        }

        return $subject;
    }

    public function mbSubstrReplace(string $search, string $replace, int $start, int $length, string $encoding = self::ENCODING): string
    {
		$startString = mb_substr($search, 0, $start, $encoding);
		$endString = mb_substr(
            $search,
            $start + $length,
            mb_strlen($search, $encoding),
            $encoding
        );

        return $startString . $replace . $endString;
	}

    public function mbReplaceBetween(string $search, string $replace, string $start, string $end, string $encoding = self::ENCODING): string
    {
        $pos = mb_strpos($search, $start, 0, $encoding);
        $searchStart = $pos === false ? 0 : $pos + mb_strlen($start, $encoding);

        $pos = mb_strpos($search, $end, $searchStart, $encoding);
        $searchEnd = $pos === false ? mb_strlen($search, $encoding) : $pos;

        return $this->mbSubstrReplace($search, $replace, $searchStart, $searchEnd - $searchStart, $encoding);
    }

    /**
     * @param array<int, string> $haystack
     */
    public function mbInArray(array $haystack, string $needle): bool
    {
        foreach ($haystack as $char) {
            //if (mb_ord($char) === mb_ord($needle)) {
            if ($this->mbOrd($char) === $this->mbOrd($needle)) {
                return true;
            }
        }

        return false;
    }

    public function mbUcfirst(string $string, string $encoding = self::ENCODING): string
    {
        $strlen = mb_strlen($string, $encoding);
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, $strlen - 1, $encoding);

        return mb_strtoupper($firstChar, $encoding) . $then;
    }

    // trim для utf8
	public function mbTrim(string $string): string
    {
		//return trim($string, "\xC2\xA0\n");
		//return preg_replace('~\x{00a0}~siu', '', $string);
		return preg_replace('/^[\pZ\pC]+([\PZ\PC]*)[\pZ\pC]+$/u', '$1', $string);
	}

    // ord для utf8
    public function mbOrd(string $s): int
    {
        return (int) ($s = unpack('C*',$s[0].$s[1].$s[2].$s[3]))&&$s[1]<(1<<7)?$s[1]:
        ($s[1]>239&&$s[2]>127&&$s[3]>127&&$s[4]>127?(7&$s[1])<<18|(63&$s[2])<<12|(63&$s[3])<<6|63&$s[4]:
        ($s[1]>223&&$s[2]>127&&$s[3]>127?(15&$s[1])<<12|(63&$s[2])<<6|63&$s[3]:
        ($s[1]>193&&$s[2]>127?(31&$s[1])<<6|63&$s[2]:0)));
    }

    public function getFirstSentence(string $text, bool $strict = false, string $end = '.?!'): string
    {
	    preg_match("/^[^$end]+[$end]/", $text, $result);
	    if (empty($result)) {
	        return ($strict ? false : $text);
	    }

	    return $this->mbTrim($result[0]);
	}

    public function replaceNbspsWithSpaces(string $text): string
    {
        return str_replace("\xc2\xa0", "\x20", $text);
    }

    public function replaceLineBreaksWithSpaces(string $text): string
    {
        return str_replace("\x0d\x0a", "\x20", $text);
    }
}