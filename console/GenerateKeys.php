<?php

class GenerateKeys
{
    private $envPath = __DIR__ . "/../.env";
    private $value = 64;
    private $envExample = __DIR__ . "/../.env.example";

    public function __construct()
    {
        $this->generateServerKey();
        $this->generateAccessKey();
    }

    private function generateENVfile()
    {
        Cli::info("Copying .env.example file...\n");
        if (file_exists($this->envExample)) {
            if (copy($this->envExample, $this->envPath)) {
                Cli::info(".env file created successfully!\n");
            } else {
                Cli::error("Failed to copy .env.example to .env\n");
                exit(1);
            }
        } else {
            Cli::error(".env.example file does not exist\n");
            exit(1);
        }
    }

    private function generateServerKey()
    {
        $value = bin2hex(openssl_random_pseudo_bytes($this->value / 2));
        if (!file_exists($this->envPath)) {
            Cli::warning("No .env file found.  Creating....\n");
            $this->generateENVfile();
        }

        $envContent = file_get_contents($this->envPath);
        if ($envContent === false) {
            Cli::error("Failed to read .env file\n");
            exit(1);
        }

        $lines = explode("\n", $envContent);
        $found = false;
        $updated = false;

        foreach ($lines as &$line) {
            if (strpos($line, "SERVERKEY=") === 0) {
                $currentValue = explode('=', $line, 2)[1] ?? '';
                if (empty($currentValue)) {
                    $line = "SERVERKEY={$value}";
                    $updated = true;
                }
                $found = true;
                break;
            }
        }

        if (!$found) {
            $lines[] = "SERVERKEY={$value}";
            $updated = true;
        }

        if ($updated) {
            $newContent = implode("\n", $lines);
            if (file_put_contents($this->envPath, $newContent) === false) {
                Cli::error("Failed to write to .env file \n");
                exit(1);
            }
            Cli::success('SERVERKEY has been generated ' . PHP_EOL);
        } else {
            Cli::error("SERVERKEY already has a value");
            exit(1);
        }
    }

    private function generateAccessKey()
    {
        $value = bin2hex(openssl_random_pseudo_bytes($this->value / 2));
        if (!file_exists($this->envPath)) {
            Cli::warning("No .env file found. Creating...\n");
            $this->generateENVfile();
        }

        $envContent = file_get_contents($this->envPath);
        if ($envContent === false) {
            Cli::error("Failed to read .env file\n");
            exit(1);
        }

        $lines = explode("\n", $envContent);
        $found = false;
        $updated = false;

        foreach ($lines as &$line) {
            if (strpos($line, "ACCESS_KEY=") === 0) {
                $currentValue = explode('=', $line, 2)[1] ?? '';
                if (empty($currentValue)) {
                    $line = "ACCESS_KEY={$value}";
                    $updated = true;
                }
                $found = true;
                break;
            }
        }

        if (!$found) {
            $lines[] = "ACCESS_KEY={$value}";
            $updated = true;
        }

        if ($updated) {
            $newContent = implode("\n", $lines);
            if (file_put_contents($this->envPath, $newContent) === false) {
                Cli::error("Failed to write to .env file\n");
                exit(1);
            }
            Cli::success('ACCESS_KEY has been generated ' . PHP_EOL);
        } else {
            Cli::error("ACCESS_KEY already has a value\n");
        }
    }
}
