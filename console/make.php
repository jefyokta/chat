<?php

function createModelFile($filename)
{
    $namespace = $filename . 'Model';
    $fileContent = "<?php\n\nnamespace oktaa\\model\\;\n\nuse oktaa\\Database\\Database;\n\nclass $filename extends Database\n{\n    protected string \$table = '{table}';\n    public \$name = '{table}';\n    protected array \$definition = [\n        'id' => 'INT AUTO_INCREMENT PRIMARY KEY'\n        // Define other columns here\n    ];\n    protected array \$fillable = [\n        // Define your fillable columns here\n    ];\n}\n";
    file_put_contents(__DIR__ . "/../app/models/$namespace.php", $fileContent);
    Cli::success("Model file created successfully: $namespace.php");
}

function createAppFile($filename)
{
    $namespace = $filename . 'App';
    $fileContent = "<?php\n\nnamespace oktaa\\App;\n\nuse oktaa\\App\\App;\n\nclass $filename".'App'." extends App\n{\n    public function __construct()\n    {\n        \$this->get(\"/\", function (\$req, \$res) {\n            \$res->Json([\"hello\"]);\n      });\n  /*...another routes */ \n    }\n}\n";
    file_put_contents(__DIR__ . "/../app/app/apps/$namespace.php", $fileContent);
    Cli::success("App file created successfully: $namespace.php");
}

function getValidInput($prompt)
{
    fwrite(STDOUT, $prompt);
    $input = trim(fgets(STDIN));
    while (empty($input)) {
        fwrite(STDOUT, "Input cannot be empty. $prompt");
        $input = trim(fgets(STDIN));
    }
    return $input;
}

function fileTypeHandler(string $fileType)
{
    if ($fileType === 'model') {
        if (isset($argv[3])) {
            createModelFile($argv[3]);
        } else {
            $filename = getValidInput("Model Name? \n");
            createModelFile($filename);
        }
    } elseif ($fileType === 'app') {
        if (isset($argv[3])) {
            createAppFile($argv[3]);
        } else {
            $filename = getValidInput("App Name? \n");
            createAppFile($filename);
        }
    } else {
        fwrite(STDERR, "Unknown file type `$fileType`\n");
        exit(1);
    }
}

function handleMakeCommand($argv)
{
    if (isset($argv[2])) {
        $fileType = $argv[2];
        if ($fileType === 'model' || $fileType === 'app') {
            fileTypeHandler($fileType);
        } else {
            fwrite(STDERR, "Unknown file type `$fileType`\n");
            exit(1);
        }
    } else {
        fwrite(STDOUT, "What file do you want to make? \n");
        fwrite(STDOUT, "Usage: php okta make {kind} {filename} \n");
        Cli::info(" Option {kind}");
        fwrite(STDOUT, " -model\n");
        fwrite(STDOUT, " -app\n");

        $input = trim(fgets(STDIN));
        $parts = explode(' ', $input, 2);
        $fileType = $parts[0] ?? '';
        $filename = $parts[1] ?? '';

        if (empty($fileType)) {
            fwrite(STDERR, "File type cannot be empty.\n");
            exit(1);
        }

        if ($fileType === 'model') {
            $filename = $filename ?: getValidInput("Model Name? \n");
            createModelFile($filename);
        } elseif ($fileType === 'app') {
            $filename = $filename ?: getValidInput("App Name? \n");
            createAppFile($filename);
        } else {
            fwrite(STDERR, "Unknown file type `$fileType`\n");
            exit(1);
        }
    }
}
