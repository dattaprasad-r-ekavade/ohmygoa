<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Sanitize input data
        $input = $request->all();
        
        array_walk_recursive($input, function (&$value) {
            if (is_string($value)) {
                // Remove null bytes
                $value = str_replace(chr(0), '', $value);
                
                // Trim whitespace
                $value = trim($value);
                
                // Convert special characters to HTML entities (XSS protection)
                // Only for non-editor fields
                if (!$this->isRichTextField($value)) {
                    $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
            }
        });

        $request->merge($input);

        return $next($request);
    }

    /**
     * Check if field contains rich text content.
     */
    protected function isRichTextField(string $value): bool
    {
        // If value contains HTML tags, assume it's rich text
        return $value !== strip_tags($value);
    }
}
