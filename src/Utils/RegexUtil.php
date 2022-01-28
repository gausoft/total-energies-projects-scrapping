<?php

namespace App\Utils;

class RegexUtil
{
    public static function extractUrl($text)
    {
        $urlPattern = "(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?";
        preg_match("/$urlPattern/i", $text, $match);

        if (empty($match)) {
            preg_match('/team-avatar-\d+.jpg/', $text, $match);
            $imageUrl = "https://startupper.totalenergies.com/" . $match[0];
        } else {
            $imageUrl = $match[0];
        }

        return $imageUrl;
    }
}
