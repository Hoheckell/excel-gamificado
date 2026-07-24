<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class MissionAttachment implements ValidationRule
{
    private const ALLOWED_EXTENSIONS = [
        'png', 'jpg', 'xls', 'xlsx', 'docs', 'doc', 'csv', 'txt', 'pdf',
    ];

    private const MIME_TYPES_BY_EXTENSION = [
        'png' => ['image/png'],
        'jpg' => ['image/jpeg'],
        'pdf' => ['application/pdf'],
        'csv' => ['text/plain', 'text/csv', 'application/csv'],
        'txt' => ['text/plain'],
        'xls' => ['application/vnd.ms-excel', 'application/x-ole-storage', 'application/octet-stream'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/octet-stream'],
        'doc' => ['application/msword', 'application/x-ole-storage', 'application/octet-stream'],
        'docs' => ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/octet-stream'],
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            $fail('O :attribute deve ser um arquivo válido.');

            return;
        }

        $extension = strtolower($value->getClientOriginalExtension());
        $mimeType = $value->getMimeType();

        if (mb_strlen($value->getClientOriginalName()) > 255) {
            $fail('O nome do :attribute não pode ultrapassar 255 caracteres.');
        }

        if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)
            || ! in_array($mimeType, self::MIME_TYPES_BY_EXTENSION[$extension] ?? [], true)) {
            $fail('O :attribute deve ser PNG, JPG, XLS, XLSX, DOCS, DOC, CSV, TXT ou PDF.');
        }

        if ($value->getSize() > 3 * 1024 * 1024) {
            $fail('O :attribute não pode ultrapassar 3 MB.');
        }
    }
}
