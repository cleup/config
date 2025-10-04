<?php

namespace Cleup\Configuration\Environment;

class Parser
{
    private const COMMENT_PREFIX = '#';
    private const DELIMITER = '=';
    private const QUOTES = ['"', "'"];

    /**
     * Parse .env file with improved functionality
     * 
     * @param string $filePath
     * @return array
     */
    public function parse($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("The file {$filePath} doesn't exists");
        }

        $lines = $this->readFile($filePath);
        $content = [];

        foreach ($lines as $line) {
            if ($this->isEmptyLine($line) || $this->isComment($line)) {
                continue;
            }

            [$key, $value] = $this->parseLine($line);
            
            if ($key !== null) {
                $content[$key] = $this->parseValue($value, $content);
            }
        }

        return $content;
    }

    /**
     * Read file and split into lines
     * 
     * @param string $filePath
     * @return array
     */
    private function readFile(string $filePath): array
    {
        $content = file_get_contents($filePath);
        return explode(PHP_EOL, $content);
    }

    /**
     * Check if line is empty
     * 
     * @param string $line
     * @return bool
     */
    private function isEmptyLine(string $line): bool
    {
        return trim($line) === '';
    }

    /**
     * Check if line is a comment
     * 
     * @param string $line
     * @return bool
     */
    private function isComment(string $line): bool
    {
        return strpos(trim($line), self::COMMENT_PREFIX) === 0;
    }

    /**
     * Parse line into key-value pair
     * 
     * @param string $line
     * @return array
     */
    private function parseLine(string $line): array
    {
        $line = $this->removeInlineComment($line);
        $parts = explode(self::DELIMITER, $line, 2);

        if (count($parts) !== 2) {
            return [null, null];
        }

        $key = trim($parts[0]);
        $value = trim($parts[1]);

        return [$key, $value];
    }

    /**
     * Remove inline comments
     * 
     * @param string $line
     * @return string
     */
    private function removeInlineComment(string $line): string
    {
        $quoted = false;
        $quoteChar = null;
        $result = '';

        for ($i = 0; $i < strlen($line); $i++) {
            $char = $line[$i];

            if (in_array($char, self::QUOTES)) {
                if ($quoteChar === null) {
                    $quoteChar = $char;
                    $quoted = true;
                } elseif ($quoteChar === $char) {
                    $quoted = false;
                }
            }

            if (!$quoted && $char === self::COMMENT_PREFIX) {
                break;
            }

            $result .= $char;
        }

        return $result;
    }

    /**
     * Parse and process value
     * 
     * @param string $value
     * @param array $environment
     * @return mixed
     */
    private function parseValue(string $value, array $environment)
    {
        $value = $this->removeQuotes($value);
        $value = $this->resolveVariables($value, $environment);

        return match (strtolower($value)) {
            'null' => null,
            'true' => true,
            'false' => false,
            default => $this->castNumericValue($value)
        };
    }

    /**
     * Remove surrounding quotes
     * 
     * @param string $value
     * @return string
     */
    private function removeQuotes(string $value): string
    {
        foreach (self::QUOTES as $quote) {
            if (str_starts_with($value, $quote) && str_ends_with($value, $quote)) {
                return substr($value, 1, -1);
            }
        }

        return $value;
    }

    /**
     * Resolve variable expansions
     * 
     * @param string $value
     * @param array $environment
     * @return string
     */
    private function resolveVariables(string $value, array $environment): string
    {
        return preg_replace_callback(
            '/\$\{([^}]+)\}/',
            function ($matches) use ($environment) {
                $varParts = explode(':=', $matches[1]);
                $varName = trim($varParts[0]);
                
                return $environment[$varName] ?? $varParts[1] ?? '';
            },
            $value
        );
    }

    /**
     * Cast numeric strings to appropriate types
     * 
     * @param string $value
     * @return mixed
     */
    private function castNumericValue(string $value)
    {
        if (!is_numeric($value)) {
            return $value;
        }

        return strpos($value, '.') !== false 
            ? (float) $value 
            : (int) $value;
    }
}