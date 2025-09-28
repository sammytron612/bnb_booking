<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class SecureFileUpload implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            $fail('The :attribute must be a valid file.');
            return;
        }

        // Check if file was uploaded successfully
        if (!$value->isValid()) {
            $fail('The :attribute upload failed.');
            return;
        }

        // Validate MIME type (don't trust client-provided type)
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        $detectedMime = mime_content_type($value->getPathname());

        if (!in_array($detectedMime, $allowedMimes)) {
            $fail('The :attribute must be a valid image file (JPEG, PNG, GIF, WebP).');
            return;
        }

        // Check file extension matches MIME type
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower($value->getClientOriginalExtension());

        if (!in_array($extension, $allowedExtensions)) {
            $fail('The :attribute must have a valid image extension.');
            return;
        }

        // Validate file size (2MB max)
        if ($value->getSize() > 2097152) {
            $fail('The :attribute must not exceed 2MB.');
            return;
        }

        // Check for malicious content in file header
        $fileContent = file_get_contents($value->getPathname(), false, null, 0, 1024);

        // Check for PHP tags in image files (common attack vector)
        if (strpos($fileContent, '<?php') !== false ||
            strpos($fileContent, '<?=') !== false ||
            strpos($fileContent, '<script') !== false) {
            $fail('The :attribute contains potentially malicious content.');
            return;
        }

        // Validate image dimensions using getimagesize
        $imageInfo = @getimagesize($value->getPathname());

        if ($imageInfo === false) {
            $fail('The :attribute is not a valid image file.');
            return;
        }

        // Check minimum and maximum dimensions
        [$width, $height] = $imageInfo;

        if ($width < 200 || $height < 200) {
            $fail('The :attribute must be at least 200x200 pixels.');
            return;
        }

        if ($width > 3000 || $height > 3000) {
            $fail('The :attribute must not exceed 3000x3000 pixels.');
            return;
        }

        // Additional security: Check for suspicious file names
        $originalName = $value->getClientOriginalName();
        if (preg_match('/[^\w\-_\.]/', pathinfo($originalName, PATHINFO_FILENAME))) {
            $fail('The :attribute filename contains invalid characters.');
            return;
        }
    }
}
