<?php

namespace App;

class Format
{

	public static function getTimeElapsed($timestamp, $labels = true)
	{
		$since  = time() - $timestamp;
		$future = ($since < 0);
		$since  = abs($since);
		
		$chunks = [
			[31536000, "year"],
			[2592000, "month"],
			[604800, "week"],
			[86400, "day"],
			[3600, "hour"],
			[60, "minute"],
			[1, "second"],
		];
		for ($i = 0, $j = count($chunks); $i < $j; ++$i) {
			$seconds = $chunks[$i][0];
			$name    = $chunks[$i][1];
			if (($count = floor($since / $seconds)) != 0)
				break;
		}
		$print = ($count == 1 ? "1 " . $name
							  : $count . " " . $name . "s"
		);
		if (!$labels)
			return $print;
		return ($future ? "In " . $print  : $print . " ago");
	}

	public static function complexTimeElapsed($secs)
	{
		$bit = [
			" year"   => $secs / 31556926 % 12,
			" week"   => $secs / 604800 % 52,
			" day"    => $secs / 86400 % 7,
			" hour"   => $secs / 3600 % 24,
			" minute" => $secs / 60 % 60,
			" second" => $secs % 60,
		];
		foreach($bit as $k => $v) {
			if ($v < 1)
				continue;
			
			$ret[] = $v . $k . ($v > 1 ? "s" : "");
		}
		if (isset($ret) && !empty($ret) && is_array($ret) && count($ret) > 1)
			array_splice($ret, count($ret) - 1, 0, "and");
		if (isset($ret) && !empty($ret) && is_array($ret))
			return join(" ", $ret);
	}

	public static function formatTimestamp($timestamp)
	{
		return "<attr title='" . date("r", $timestamp) . "'>" . self::getTimeElapsed($timestamp) . "</attr>";
	}

	public static function formatSimpleTimestamp($timestamp)
	{
		global $config;
		return "<attr title='" . date("r", $timestamp) . "'>" . date($config['dateFormatSimple'], $timestamp) . "</attr>";
	}

}
