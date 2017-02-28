<?php

class Md5Generation
{
	private $config;
	private $options;
	private $validated = false;

	function __construct($config, $options)
	{
		$this->config = $config;
		$this->options = $options;
	}

	function validateConfig()
	{
		if (empty($this->config['input']))
		{
			printf("ERR: not specifies source file path\n");
			return false;
		}

		if (empty($this->config['output']))
		{
			printf("ERR: not output filename or output directory\n");
			return false;
		}

		if (!$this->config['quiet'])
		{
			$config = $this->config;
			$options = $this->options;
    		print("=============================\n");
			print("config:\n");
    		for ($i = 0; $i < count($options); $i++)
    		{
        		$key = $options[$i][1];
        		$value = $config[$key];
        		if ($value != null)
        		{
            		printf("\t%s = %s\n", $key, $value);
        		}
    		}
    		print("=============================\n");
    		print("\n");
		}

		// check file path
		if (!file_exists($this->config['input']))
		{
			printf("ERR: invalid source file path\n");
			return false;
		}

		// check output path
		if (empty($this->config['output']))
		{
			printf("ERR: invalid output\n");
			return false;
		}	

		if (!file_exists($this->config["projectDir"])) {
			printf("ERR: invalid project dir\n");
			return false;
		}

		$this->validated = true;
		return true;
	}

	function run()
	{
		if (!$this->validated)
		{
			print("ERR: invalid config\n");
			return false;
		}

		$inputArray = $this->formatLuaTableToPhpArr(file($this->config["input"]));
		$outputFile = fopen($this->config["output"], "w");

		if ($this->config["name"]) {
			fwrite($outputFile, $this->config["name"]."=");
		}
		
		fwrite($outputFile, "{\n");
		$times = 0;
		foreach ($inputArray as $key => $value) {
			if (is_file($value)) {
				$times = $times+1;
				$md5 = md5_file($value);

				fwrite($outputFile, "\t$key = \"$md5\",\n");
			} else {
				printf("ERR: NOT A FILE $key => $value\n");
			}
		}
		fwrite($outputFile, "}\n");
		fclose($outputFile);

		printf("TOTAL FILES: $times\n");
		printf("create output files in %s .\n", $this->config['output']);
		printf("done.\n");
	}

	function formatLuaTableToPhpArr($arr) {
		$resultArr = array();
		$arrNameStake = array();
		$absolutePath = $this->config["projectDir"]."res/";
		foreach ($arr as $line) {
			if (FALSE == strpos($line, "--")) { // 屏蔽掉注释行
				$line = trim($line);
				$line = preg_replace(array("/\s/", "/\,/", "/}/", "/{/", "/\"/", "/\'/"), "", $line); // 清掉符号空格
				$tmpArr = explode("=", $line);
				if ($tmpArr[0] != "" and $tmpArr[1] != "") { // 屏蔽掉空白行或则不合格的行
					$resultArr[$tmpArr[0]] = $absolutePath.$tmpArr[1];
				}
			} else {
			}
		}
		return $resultArr;
	}
}
