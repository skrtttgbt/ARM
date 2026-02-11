<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('normalize_ph_mobile')) {
    /**
     * Normalize a PH mobile number to +639XXXXXXXXX.
     * Accepts +639XXXXXXXXX, 09XXXXXXXXX, or 9XXXXXXXXX.
     * Returns empty string if invalid.
     */
    function normalize_ph_mobile($value) {
        if ($value === null) {
            return '';
        }

        $raw = trim((string)$value);
        if ($raw === '') {
            return '';
        }

        // Keep only digits and leading plus
        $has_plus = strpos($raw, '+') === 0;
        $digits = preg_replace('/\D/', '', $raw);

        if ($has_plus) {
            if (strlen($digits) === 12 && strpos($digits, '63') === 0) {
                return '+' . $digits;
            }
            return '';
        }

        if (strlen($digits) === 12 && substr($digits, 0, 2) === '63') {
            return '+' . $digits;
        }

        if (strlen($digits) === 11 && substr($digits, 0, 2) === '09') {
            return '+63' . substr($digits, 1);
        }

        if (strlen($digits) === 10 && substr($digits, 0, 1) === '9') {
            return '+63' . $digits;
        }

        if (strlen($digits) === 9) {
            return '+639' . $digits;
        }

        return '';
    }
}

if (!function_exists('is_valid_ph_mobile')) {
    function is_valid_ph_mobile($value) {
        return normalize_ph_mobile($value) !== '';
    }
}
