<?php
declare(strict_types=1);

namespace Core;

final class Lang
{
    private const SUPPORTED = ['en', 'ar'];
    private const RTL = ['ar'];

    /** @var array<string, array<string, mixed>> */
    private static array $cache = [];

    public static function current(): string
    {
        $lang = $_SESSION['lang'] ?? 'en';
        return in_array($lang, self::SUPPORTED, true) ? $lang : 'en';
    }

    public static function dir(?string $lang = null): string
    {
        $lang ??= self::current();
        return in_array($lang, self::RTL, true) ? 'rtl' : 'ltr';
    }

    public static function isAr(): bool
    {
        return self::current() === 'ar';
    }

    /**
     * Fetch a translated string by dot-notation key, e.g. "nav.home".
     * Falls back to English, then to the key itself, so a missing
     * translation never breaks the page.
     */
    public static function get(string $key, array $replace = []): string
    {
        $value = self::lookup($key, self::current()) ?? self::lookup($key, 'en') ?? $key;

        foreach ($replace as $search => $val) {
            $value = str_replace(':' . $search, (string)$val, $value);
        }

        return $value;
    }

    private static function lookup(string $key, string $lang): ?string
    {
        $dict = self::dictionary($lang);
        $segments = explode('.', $key);
        $node = $dict;

        foreach ($segments as $segment) {
            if (!is_array($node) || !array_key_exists($segment, $node)) {
                return null;
            }
            $node = $node[$segment];
        }

        return is_string($node) ? $node : null;
    }

    /** @return array<string, mixed> */
    private static function dictionary(string $lang): array
    {
        if (!isset(self::$cache[$lang])) {
            $file = __DIR__ . '/../app/lang/' . $lang . '.php';
            self::$cache[$lang] = is_file($file) ? require $file : [];
        }
        return self::$cache[$lang];
    }
}
