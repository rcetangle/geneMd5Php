<?php

// ini_set('memory_limit','256M');
require_once(__DIR__ . '/Md5Generation.php');

$options = array(
    array('h',   'help',       0,      false,       'show help'),
    array('i',   'input',      1,      null,        'source file path'),
    array('o',   'output',     1,      null,        'output filename | output directory'),
    array('d',   'projectDir', 1,      null,        'project dir'),
    array('n',   'name',       1,      null,        'table name | array name'),
    array('c',   'config',     1,      null,        'load options from config file'),
    array('q',   'quiet',      0,      false,       'quiet'),
);

function errorhelp()
{
    print("\nshow help:\n    gene_md5 -h\n\n");
}

function help()
{
    global $options;

    echo <<<EOT

usage: gene_md5 -i input -o output ...

options:

EOT;

    for ($i = 0; $i < count($options); $i++)
    {
        $o = $options[$i];
        printf("    -%s %s\n", $o[0], $o[4]);
    }

    echo <<<EOT

config file format:

    return array(
        'input'      => source file path,
        'output'     => output filename or output directory,
        'projectDir' => dir of project,
        'name'       => name of output data,
    );

examples:

    # gene res/*.* to resnew/
    gene_md5 -i res -o resnew -d projectDir -n dataName

    # load options from config file
    gene_md5 -c my_config.lua

EOT;

}

function fetchCommandLineArguments($arg, $options, $minNumArgs = 0)
{
    if (!is_array($arg) || !is_array($options))
    {
        print("ERR: invalid command line arguments");
        return false;
    }

    $config = array();
    $newOptions = array();
    for ($i = 0; $i < count($options); $i++)
    {
        $option = $options[$i];
        $newOptions[$option[0]] = $option;
        $config[$option[1]] = $option[3];
    }
    $options = $newOptions;

    $i = 1;
    while ($i < count($arg))
    {
        $a = $arg[$i];
        if ($a{0} != '-')
        {
            printf("ERR: invalid argument %d: %s", $i, $a);
            return false;
        }

        $a = substr($a, 1);
        if (!isset($options[$a]))
        {
            printf("ERR: invalid argument %d: -%s", $i, $a);
            return false;
        }

        $key = $options[$a][1];
        $num = $options[$a][2];
        $default = $options[$a][3];

        if ($num == 0)
        {
            $config[$key] = true;
        }
        else
        {
            $values = array();
            for ($n = 1; $n <= $num; $n++)
            {
                $values[] = $arg[$i + $n];
            }
            if (count($values) == 1)
            {
                $config[$key] = $values[0];
            }
            else
            {
                $config[$key] = $values;
            }
        }

        $i = $i + $num + 1;
    }

    return $config;
}

// ----

print("\n");
if ($argc < 2)
{
    help();
    return(1);
}

$config = fetchCommandLineArguments($argv, $options, 4);
if (!$config)
{
    errorhelp();
    return(1);
}

if ($config['help'])
{
    help();
    return(0);
}

if ($config['config'])
{
    $configFilename = $config['config'];
    if (file_exists($configFilename))
    {
        $config = @include($configFilename);
    }
    else
    {
        $config = null;
    }

    if (!is_array($config))
    {
        printf("ERR: invalid config file, %s\n", $configFilename);
        errorhelp();
        return(1);
    }
}

$packer = new Md5Generation($config, $options);
if ($packer->validateConfig())
{
    return($packer->run());
}
else
{
    errorhelp();
    return(1);
}
