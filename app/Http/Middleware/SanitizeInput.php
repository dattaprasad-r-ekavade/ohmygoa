<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Fields that are allowed to contain HTML content.
     * These should be handled with HTML Purifier or similar in their respective controllers.
     */
    protected $richTextFields = [
        'description',
        'content',
        'body',
        'bio',
        'about',
        'notes',
        'instructions',
        'terms',
        'policy',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Sanitize input data
        $input = $request->all();
        
        $this->sanitizeArray($input);

        $request->merge($input);

        return $next($request);
    }

    /**
     * Recursively sanitize an array.
     */
    protected function sanitizeArray(array &$data, string $parentKey = ''): void
    {
        foreach ($data as $key => &$value) {
            $fullKey = $parentKey ? "{$parentKey}.{$key}" : $key;
            
            if (is_array($value)) {
                $this->sanitizeArray($value, $fullKey);
            } elseif (is_string($value)) {
                // Remove null bytes
                $value = str_replace(chr(0), '', $value);
                
                // Trim whitespace
                $value = trim($value);
                
                // Only allow HTML in explicitly whitelisted fields
                if (!$this->isAllowedRichTextField($fullKey)) {
                    // Convert special characters to HTML entities for XSS protection
                    $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
                // Note: Rich text fields should be sanitized with HTML Purifier in the controller
            }
        }
    }

    /**
     * Check if field is in the rich text whitelist.
     */
    protected function isAllowedRichTextField(string $fieldName): bool
    {
        // Check if the field name ends with any of the whitelisted names
        foreach ($this->richTextFields as $richField) {
            if (str_ends_with($fieldName, $richField)) {
                return true;
            }
        }
        
        return false;
    }
}
