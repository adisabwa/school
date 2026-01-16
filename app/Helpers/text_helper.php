<?php

function getValueFromOption($label, $options, $sim_rate = 0.7)
{
    if (!$label || empty($options)) {
        return false;
    }

    // Jika associative array → ambil values
    if (!array_is_list($options) && is_array($options)) {
        $options = array_values($options);
    }

    $sim = [];

    foreach ($options as $i => $element) {
        if (is_array($element))
            $text2 = $element['match']
                ?? $element['label'] ?? '';
        else
            $text2 = $element->match ?? $element->label ?? '';


        $text1 = trim(mb_strtolower($label));
        $text2 = trim(mb_strtolower($text2));

        $sim[$i] = isSimilar($text1, $text2);
    }

    if (empty($sim)) {
        return false;
    }

    $maxSim = max($sim);

    if ($maxSim > $sim_rate) {
        $index = array_search($maxSim, $sim, true);
        return (is_array($options[$index]) ? $options[$index]['value'] : $options[$index]->value) ?? false;
    }

    return false;
}

function isSimilar(string $s1, string $s2, bool $caseSensitive = false): float
{
    if (!$caseSensitive) {
        $s1 = mb_strtolower($s1);
        $s2 = mb_strtolower($s2);
    }

    if ($s1 === $s2) {
        return 1.0;
    }

    $len1 = mb_strlen($s1);
    $len2 = mb_strlen($s2);

    if ($len1 === 0 || $len2 === 0) {
        return 0.0;
    }

    $maxLength = max($len1, $len2);
    $maxDist = max(0, intdiv($maxLength, 2) - 1);

    $match = 0;
    $hashS1 = array_fill(0, $len1, 0);
    $hashS2 = array_fill(0, $len2, 0);

    // Matching characters
    for ($i = 0; $i < $len1; $i++) {
        $start = max(0, $i - $maxDist);
        $end   = min($len2 - 1, $i + $maxDist);

        for ($j = $start; $j <= $end; $j++) {
            if ($s1[$i] === $s2[$j] && $hashS2[$j] === 0) {
                $hashS1[$i] = 1;
                $hashS2[$j] = 1;
                $match++;
                break;
            }
        }
    }

    if ($match === 0) {
        return 0.0;
    }

    // Transpositions
    $t = 0;
    $point = 0;

    for ($i = 0; $i < $len1; $i++) {
        if ($hashS1[$i]) {
            while ($hashS2[$point] === 0) {
                $point++;
            }
            if ($s1[$i] !== $s2[$point]) {
                $t++;
            }
            $point++;
        }
    }

    $t /= 2;

    $jaro = (
        ($match / $len1) +
        ($match / $len2) +
        (($match - $t) / $match)
    ) / 3;

    // Jaro–Winkler boost
    if ($jaro > 0.7) {
        $prefix = 0;
        $maxPrefix = min(4, min($len1, $len2));

        for ($i = 0; $i < $maxPrefix; $i++) {
            if ($s1[$i] === $s2[$i]) {
                $prefix++;
            } else {
                break;
            }
        }

        $jaro += 0.1 * $prefix * (1 - $jaro);
    }

    return round($jaro, 6);
}

