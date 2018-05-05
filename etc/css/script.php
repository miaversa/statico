<?php
// Tachyons v4.9.1
error_reporting(E_ALL);

// Terceira etapa emite os ultimos seletores que possuem virgula e ponto-e-virgula
function lineWithCommaThirdStep($set, &$result, $line, $linenum)
{
	if (strpos($line, ':') !== false) {
		$selector = substr($line, 0, strpos($line, ':'));
		$r = [
			'required' => false,
			'selector' => $selector,
			'rule' => $line,
			'comment' => "type: thirdStep, file: {$set['filename']}, line: {$linenum}"
		];
		$result[$set['media']][] = $r;
		return;
	}
	
	print "/* {$line} */\n";
}

function lineWithCommaSecondStep($set, &$result, $line, $linenum)
{
	if (strpos($line, 'hover-') !== false && strpos($line, ':hover') !== false && strpos($line, ':hover') !== false) {
		$selector = substr($line, 0, strpos($line, ':'));
		$r = [
			'required' => false,
			'selector' => $selector,
			'rule' => $line,
			'comment' => "type: secondStep, file: {$set['filename']}, line: {$linenum}"
		];
		$result[$set['media']][] = $r;
	} else {
		lineWithCommaThirdStep($set, $result, $line, $linenum);
	}
}

function lineWithCommaFirstStep($set, &$result, $line, $linenum)
{
	$bracket = strpos($line, '{');
	$beforeBracket = substr($line, 0, $bracket);
	
	if (strpos($beforeBracket, ',') === false && strpos($beforeBracket, ':') === false) {
		$r = [
			'required' => false,
			'selector' => substr($line, 0, strpos($line, '{')),
			'rule' => $line,
			'comment' => "type: firstStep, file: {$set['filename']}, line: {$linenum}"
		];
		$result[$set['media']][] = $r;
	} else {
		lineWithCommaSecondStep($set, $result, $line, $linenum);
	}
}

function fileWork($set, &$result)
{
	$result[$set['media']] = [];
	
	$handle = fopen($set['filename'], "r");
	
	if ($handle) {
		$linenum = 0;
		while (($line = fgets($handle)) !== false) {
			$linenum++;
			$line = trim($line);
			$bb = substr($line, 0, strpos($line, '{'));
			if (strpos($line, ',') !== false || strpos($bb, ':') !== false) {
				lineWithCommaFirstStep($set, $result, $line, $linenum);
			} else {
				$r = [
					'required' => false,
					'selector' => substr($line, 0, strpos($line, '{')),
					'rule' => $line,
					'comment' => "type: simplerule, file: {$set['filename']}, line: {$linenum}"
				];
				$result[$set['media']][] = $r;
			}
		}
		fclose($handle);
	} else {
		print "erro ao abrir o arquivo: {$f}\n";
		exit(1);
	}
}

function process()
{
	$result = [];
	
	$stlset = [
		['media' => 'bas', 'filename' => 'tachyons_bas.css'],
		['media' => 'min', 'filename' => 'tachyons_min.css'],
		['media' => 'mid', 'filename' => 'tachyons_mid.css'],
		['media' => 'max', 'filename' => 'tachyons_max.css'],
	];
	
	foreach($stlset as $set) {
		fileWork($set, $result);
	}
	
	foreach($result as $media => $rules) {
		foreach($rules as $k => $rule) {
			if ('' == $rule['selector']) {
				$result[$media][$k]['required'] = true;
			}
		}
	}

	return $result;
}

function pprint($result)
{
	$headers = [
		'bas' => '',
		'min' => '@media screen and (min-width:30em){',
		'mid' => '@media screen and (min-width:30em) and (max-width:60em){',
		'max' => '@media screen and (min-width:60em){',
	];
	
	foreach($result as $media => $rules) {
		$header = $headers[$media];
		
		if ('' == $header) {
			$header = '';
		} else {
			$header = "\n{$header}\n";
		}
		
		print $header;
		
		foreach($rules as $rule) {
			if ($rule['required']) {
				print "style{true, `{$rule['selector']}`, `{$rule['rule']}`},\n";
			} else {
				print "style{false, `{$rule['selector']}`, `{$rule['rule']}`},\n";
			}
		}
		
	}
}

function sprint($result)
{
	foreach($result as $media => $rules) {
		foreach($result[$media] as $rule) {
			print "{$rule['required']} - {$rule['selector']} - {$rule['rule']}\n";
		}
	}
}

function main() {
	$result = process();
	pprint($result);
}

main();
