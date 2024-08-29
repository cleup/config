<?php

namespace Cleup\Core\Configuration\Environment;

class Parser
{
    /**
     * Parse .env file
     * 
     * @param string $filePath
     * @return array
     */
    public function parse($filePath)
    {
        if (!file_exists($filePath))
            throw new \InvalidArgumentException("The file {$filePath} doesn't exists");

        $file = fopen($filePath, 'r');
        $content = [];

        while (!feof($file)) {
            $line = fgets($file);
            $line = str_replace(["\n", "\r"], '', $line);

            if (!empty($line)) {
                // if the line starts with # then it's comment
                if ($line[0] == '#') {
                    continue;
                }

                // extract key and value
                $segments = explode('=', $line, 2);
                $key = trim($segments[0]);
                $value = $segments[1];

                // remove comments from line
                if (((strlen($value) - 1) > 0) &&
                    ($value[strlen($value) - 1] != '"') &&
                    ($value[strlen($value) - 1] != '\'')
                ) {
                    $value = explode('#', $value)[0];
                }

                $isNumericString = false;
                if (
                    isset($value[0]) &&
                    (trim($value, " ")[0] == '\''
                        || trim($value, " ")[0] == '"') &&
                    is_numeric(trim($value, " \"'"))
                ) {
                    $isNumericString = true;
                }

                $value = trim($value, " \"'");
                $matches = [];

                preg_match_all('~^\$\{[^${}]*|^[^${}]*\}$|\$\{[^${}]*\}~', $value, $matches);

                if (!empty($matches[0])) {
                    foreach ($matches[0] as $var) {
                        $var = str_replace(["$", "{", "}"], '', $var);
                        $varValues = explode(':=', $var);
                        $value = str_replace(
                            '${' . $var . '}',
                            $content[$varValues[0]] ?? $varValues[1] ?? '',
                            $value
                        );
                    }
                }

                // handle data types
                if (strtolower($value) == 'null') {
                    $value = null;
                } else if (strtolower($value) == 'true') {
                    $value = true;
                } else if (strtolower($value) == 'false') {
                    $value = false;
                } else if (is_numeric($value) && !$isNumericString) {
                    if (strpos($value, '.') !== false)
                        $value = (float) $value;
                    else
                        $value = (int) $value;
                }

                $content[$key] = $value;
            }
        }

        return $content;
    }
}
