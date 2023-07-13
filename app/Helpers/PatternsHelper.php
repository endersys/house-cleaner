<?php

if (!function_exists('postal_code_pattern')) {
    function postal_code_pattern($country): string {
        if ($country) {
            return get_postal_code_patterns()[$country];
        }

        return '';
    }
}

if (!function_exists('get_postal_code_patterns')) {
    function get_postal_code_patterns(): array {
        return [
            'eua' => '00000-0000',
            'brasil' => '00000-000'
        ];
    }
}