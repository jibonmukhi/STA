<?php

namespace App\Services;

use DateTime;

class CodiceFiscaleValidator
{
    /**
     * Month codes used in Codice Fiscale
     */
    private const MONTH_CODES = [
        1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'H',
        7 => 'L', 8 => 'M', 9 => 'P', 10 => 'R', 11 => 'S', 12 => 'T'
    ];

    /**
     * Characters for checksum calculation (even positions)
     */
    private const EVEN_CHARS = [
        '0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9,
        'A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4, 'F' => 5, 'G' => 6, 'H' => 7, 'I' => 8, 'J' => 9,
        'K' => 10, 'L' => 11, 'M' => 12, 'N' => 13, 'O' => 14, 'P' => 15, 'Q' => 16, 'R' => 17, 'S' => 18,
        'T' => 19, 'U' => 20, 'V' => 21, 'W' => 22, 'X' => 23, 'Y' => 24, 'Z' => 25
    ];

    /**
     * Characters for checksum calculation (odd positions)
     */
    private const ODD_CHARS = [
        '0' => 1, '1' => 0, '2' => 5, '3' => 7, '4' => 9, '5' => 13, '6' => 15, '7' => 17, '8' => 19, '9' => 21,
        'A' => 1, 'B' => 0, 'C' => 5, 'D' => 7, 'E' => 9, 'F' => 13, 'G' => 15, 'H' => 17, 'I' => 19, 'J' => 21,
        'K' => 2, 'L' => 4, 'M' => 18, 'N' => 20, 'O' => 11, 'P' => 3, 'Q' => 6, 'R' => 8, 'S' => 12, 'T' => 14,
        'U' => 16, 'V' => 10, 'W' => 22, 'X' => 25, 'Y' => 24, 'Z' => 23
    ];

    /**
     * Remainder to check character mapping
     */
    private const CHECK_CHARS = [
        0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G', 7 => 'H', 8 => 'I', 9 => 'J',
        10 => 'K', 11 => 'L', 12 => 'M', 13 => 'N', 14 => 'O', 15 => 'P', 16 => 'Q', 17 => 'R', 18 => 'S',
        19 => 'T', 20 => 'U', 21 => 'V', 22 => 'W', 23 => 'X', 24 => 'Y', 25 => 'Z'
    ];

    /**
     * Validate Codice Fiscale format
     *
     * @param string $cf
     * @return bool
     */
    public function validateFormat(string $cf): bool
    {
        // CF must be exactly 16 characters
        if (strlen($cf) !== 16) {
            return false;
        }

        // Pattern: 6 letters + 2 digits + 1 letter + 2 digits + 1 letter + 3 alphanumeric + 1 letter
        $pattern = '/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/';
        return preg_match($pattern, strtoupper($cf)) === 1;
    }

    /**
     * Validate checksum (control character)
     *
     * @param string $cf
     * @return bool
     */
    public function validateChecksum(string $cf): bool
    {
        $cf = strtoupper($cf);
        $sum = 0;

        // Calculate sum for first 15 characters
        for ($i = 0; $i < 15; $i++) {
            $char = $cf[$i];
            // Odd positions (0, 2, 4, ...) use ODD_CHARS, even positions use EVEN_CHARS
            $sum += ($i % 2 === 0) ? self::ODD_CHARS[$char] : self::EVEN_CHARS[$char];
        }

        // Calculate expected check character
        $expectedCheck = self::CHECK_CHARS[$sum % 26];
        $actualCheck = $cf[15];

        return $expectedCheck === $actualCheck;
    }

    /**
     * Validate date encoding in CF
     *
     * @param string $cf
     * @return bool
     */
    public function validateDateEncoding(string $cf): bool
    {
        $cf = strtoupper($cf);

        // Extract year, month, day
        $year = substr($cf, 6, 2);
        $monthCode = substr($cf, 8, 1);
        $day = substr($cf, 9, 2);

        // Validate month code
        if (!in_array($monthCode, self::MONTH_CODES)) {
            return false;
        }

        // Get month number
        $month = array_search($monthCode, self::MONTH_CODES);

        // Extract day (subtract 40 if female)
        $dayNum = intval($day);
        if ($dayNum > 40) {
            $dayNum -= 40;
        }

        // Validate day is in valid range
        if ($dayNum < 1 || $dayNum > 31) {
            return false;
        }

        // Validate the date is actually valid (e.g., not February 30)
        // We'll check both possible centuries (1900s and 2000s)
        $year1900 = 1900 + intval($year);
        $year2000 = 2000 + intval($year);

        return checkdate($month, $dayNum, $year1900) || checkdate($month, $dayNum, $year2000);
    }

    /**
     * Validate gender consistency
     *
     * @param string $cf
     * @param string $gender Expected gender ('male' or 'female')
     * @return bool
     */
    public function validateGender(string $cf, string $gender): bool
    {
        $cf = strtoupper($cf);
        $day = intval(substr($cf, 9, 2));

        $isFemaleInCF = $day > 40;
        $expectedFemale = strtolower($gender) === 'female';

        return $isFemaleInCF === $expectedFemale;
    }

    /**
     * Validate date of birth consistency
     *
     * @param string $cf
     * @param string $dateOfBirth Date in Y-m-d format
     * @return bool
     */
    public function validateDateOfBirth(string $cf, string $dateOfBirth): bool
    {
        $cf = strtoupper($cf);

        try {
            $date = new DateTime($dateOfBirth);
        } catch (\Exception $e) {
            return false;
        }

        // Extract CF date components
        $cfYear = intval(substr($cf, 6, 2));
        $cfMonthCode = substr($cf, 8, 1);
        $cfDay = intval(substr($cf, 9, 2));

        // Adjust day if female (subtract 40)
        if ($cfDay > 40) {
            $cfDay -= 40;
        }

        // Get month from code
        $cfMonth = array_search($cfMonthCode, self::MONTH_CODES);
        if ($cfMonth === false) {
            return false;
        }

        // Get actual birth date components
        $birthYear = intval($date->format('y')); // Last 2 digits of year
        $birthMonth = intval($date->format('n')); // Month without leading zeros
        $birthDay = intval($date->format('j')); // Day without leading zeros

        return $cfYear === $birthYear && $cfMonth === $birthMonth && $cfDay === $birthDay;
    }

    /**
     * Extract consonants from a string
     *
     * @param string $str
     * @return string
     */
    private function extractConsonants(string $str): string
    {
        $str = strtoupper($str);
        $str = preg_replace('/[^A-Z]/', '', $str); // Remove non-letters
        return preg_replace('/[AEIOU]/', '', $str); // Remove vowels
    }

    /**
     * Extract vowels from a string
     *
     * @param string $str
     * @return string
     */
    private function extractVowels(string $str): string
    {
        $str = strtoupper($str);
        $str = preg_replace('/[^A-Z]/', '', $str); // Remove non-letters
        return preg_replace('/[^AEIOU]/', '', $str); // Keep only vowels
    }

    /**
     * Encode surname for CF
     *
     * @param string $surname
     * @return string 3-character code
     */
    private function encodeSurname(string $surname): string
    {
        $consonants = $this->extractConsonants($surname);
        $vowels = $this->extractVowels($surname);

        $code = substr($consonants . $vowels . 'XXX', 0, 3);
        return strtoupper($code);
    }

    /**
     * Encode name for CF
     *
     * @param string $name
     * @return string 3-character code
     */
    private function encodeName(string $name): string
    {
        $consonants = $this->extractConsonants($name);
        $vowels = $this->extractVowels($name);

        // Special rule: if 4+ consonants, use 1st, 3rd, 4th
        if (strlen($consonants) >= 4) {
            $code = $consonants[0] . $consonants[2] . $consonants[3];
        } else {
            $code = substr($consonants . $vowels . 'XXX', 0, 3);
        }

        return strtoupper($code);
    }

    /**
     * Validate surname encoding
     *
     * @param string $cf
     * @param string $surname
     * @return bool
     */
    public function validateSurname(string $cf, string $surname): bool
    {
        $cf = strtoupper($cf);
        $cfSurname = substr($cf, 0, 3);
        $expectedSurname = $this->encodeSurname($surname);

        return $cfSurname === $expectedSurname;
    }

    /**
     * Validate name encoding
     *
     * @param string $cf
     * @param string $name
     * @return bool
     */
    public function validateName(string $cf, string $name): bool
    {
        $cf = strtoupper($cf);
        $cfName = substr($cf, 3, 3);
        $expectedName = $this->encodeName($name);

        return $cfName === $expectedName;
    }

    /**
     * Perform full validation of Codice Fiscale
     *
     * @param string $cf
     * @param array $userData ['name', 'surname', 'date_of_birth', 'gender']
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validate(string $cf, array $userData = []): array
    {
        $errors = [];
        $cf = strtoupper(trim($cf));

        // 1. Format validation
        if (!$this->validateFormat($cf)) {
            $errors[] = 'Invalid Codice Fiscale format. Expected format: RSSMRA80A01H501U';
            return ['valid' => false, 'errors' => $errors];
        }

        // 2. Checksum validation
        if (!$this->validateChecksum($cf)) {
            $errors[] = 'Invalid checksum. The control character does not match.';
        }

        // 3. Date encoding validation
        if (!$this->validateDateEncoding($cf)) {
            $errors[] = 'Invalid date encoding in Codice Fiscale.';
        }

        // If user data is provided, perform additional validations
        if (!empty($userData)) {
            // 4. Gender validation
            if (isset($userData['gender']) && !$this->validateGender($cf, $userData['gender'])) {
                $errors[] = 'Gender does not match Codice Fiscale.';
            }

            // 5. Date of birth validation
            if (isset($userData['date_of_birth']) && !$this->validateDateOfBirth($cf, $userData['date_of_birth'])) {
                $errors[] = 'Date of birth does not match Codice Fiscale.';
            }

            // 6. Surname validation
            if (isset($userData['surname']) && !$this->validateSurname($cf, $userData['surname'])) {
                $errors[] = 'Surname does not match Codice Fiscale encoding.';
            }

            // 7. Name validation
            if (isset($userData['name']) && !$this->validateName($cf, $userData['name'])) {
                $errors[] = 'Name does not match Codice Fiscale encoding.';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
