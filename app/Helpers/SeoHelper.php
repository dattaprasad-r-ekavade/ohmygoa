<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class SeoHelper
{
    /**
     * Generate meta title.
     */
    public static function title(string $title, string $suffix = ' - OhMyGoa'): string
    {
        return $title . $suffix;
    }

    /**
     * Generate meta description.
     */
    public static function description(string $description, int $maxLength = 160): string
    {
        return Str::limit(strip_tags($description), $maxLength);
    }

    /**
     * Generate meta keywords from text.
     */
    public static function keywords(string $text, int $limit = 10): string
    {
        $text = strtolower(strip_tags($text));
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        
        $words = array_count_values(str_word_count($text, 1));
        arsort($words);
        
        $stopWords = ['the', 'and', 'for', 'are', 'but', 'not', 'you', 'with', 'this', 'that'];
        $keywords = array_diff_key($words, array_flip($stopWords));
        
        return implode(', ', array_slice(array_keys($keywords), 0, $limit));
    }

    /**
     * Generate Open Graph tags.
     */
    public static function ogTags(array $data): array
    {
        return [
            'og:title' => $data['title'] ?? '',
            'og:description' => self::description($data['description'] ?? ''),
            'og:image' => $data['image'] ?? asset('images/og-default.jpg'),
            'og:url' => $data['url'] ?? url()->current(),
            'og:type' => $data['type'] ?? 'website',
            'og:site_name' => 'OhMyGoa',
        ];
    }

    /**
     * Generate Twitter Card tags.
     */
    public static function twitterTags(array $data): array
    {
        return [
            'twitter:card' => 'summary_large_image',
            'twitter:title' => $data['title'] ?? '',
            'twitter:description' => self::description($data['description'] ?? ''),
            'twitter:image' => $data['image'] ?? asset('images/og-default.jpg'),
        ];
    }

    /**
     * Generate canonical URL.
     */
    public static function canonical(string $url = null): string
    {
        return $url ?? url()->current();
    }

    /**
     * Generate breadcrumb structured data.
     */
    public static function breadcrumbSchema(array $items): array
    {
        $itemList = [];
        foreach ($items as $index => $item) {
            $itemList[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'] ?? null,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemList,
        ];
    }

    /**
     * Generate local business structured data.
     */
    public static function localBusinessSchema(array $data): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $data['name'],
            'image' => $data['image'] ?? null,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $data['address'] ?? null,
                'addressLocality' => $data['city'] ?? null,
                'addressRegion' => $data['state'] ?? 'Goa',
                'postalCode' => $data['pincode'] ?? null,
                'addressCountry' => 'IN',
            ],
            'telephone' => $data['phone'] ?? null,
            'url' => $data['url'] ?? null,
            'priceRange' => $data['priceRange'] ?? null,
        ];
    }
}
