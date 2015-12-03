<?php

/**
 * xml解析与生成
 * @author hhu
 *
 */
class XmlUtil {

	function xmlToArray($content) {
		$res = @simplexml_load_string($content, null, LIBXML_NOCDATA);
		$res = json_decode(json_encode($res), true);
		return $res;
	}

	function _xmlToArray($contents, $output_tag = null) {
		error_reporting(0);
		if (!$contents)
			return array ();

		if (!function_exists('xml_parser_create')) {
			return array ();
		}
		$parser = xml_parser_create('UTF-8');
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 0);
		xml_parse_into_struct($parser, $contents, $xml_values);
		xml_parser_free($parser);

		if (!$xml_values)
			return;

		$xml_array = array ();
		$parents = array ();
		$opened_tags = array ();
		$arr = array ();

		$current = &$xml_array;
		$number = 0;
		foreach ($xml_values as $data) {
			unset($attributes, $value);
			extract($data);
			if ($tag == 'item') {
				if (!is_null($value))
					$result = trim($value);
				if (!isset($data['attributes']['key']) && $data['attributes']['key']) {
					$tag = $number;
					$number ++;
				} else {
					$tag = $data['attributes']['key'];
				}
			} elseif (!is_null($value)) {

				$result = trim($value);
			}
			if ($type == "open") {
				$parent[$level - 1] = &$current;

				if (!is_array($current) || (!in_array($tag, array_keys($current)))) {
					$current[$tag] = $result;
					$current = &$current[$tag];
				} else {
					if (isset($current[$tag][0])) {
						array_push($current[$tag], $result);
					} else {
						$current[$tag] = array (
								$current[$tag],
								$result
						);
					}
					$last = count($current[$tag]) - 1;
					$current = &$current[$tag][$last];
				}
			} elseif ($type == "complete") {
				if (!isset($current[$tag])) {
					$current[$tag] = $result;
				} else {
					if ((is_array($current[$tag]) && $get_attributes == 0) or (isset($current[$tag][0]) && is_array($current[$tag][0]) && $get_attributes == 1)) {
						array_push($current[$tag], $result);
					} else {
						$current[$tag] = array (
								$current[$tag],
								$result
						);
					}
				}
			} elseif ($type == 'close') {
				$current = &$parent[$level - 1];
			}
		}
		if ($tag == 'item') {
			$number = 0;
		}

		if ($output_tag) {
			return ($xml_array[$output_tag]);
		} else {
			return ($xml_array);
		}
	}

	function getPath($xml, $tagName, $attr = null){
		$parser = xml_parser_create('UTF-8');
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 0);
		xml_parse_into_struct($parser, $xml, $xml_values);
		xml_parser_free($parser);

		$node = null;

		foreach ($xml_values as $k => $v) {
			if ($tagName == $v['attributes']['type']) {
				if ($attr) {
					if (count(array_diff_assoc($attr, $v['attributes'])) == 0) {
						$node = &$xml_values[$k];
						break;
					}
				} else {
					$node = &$xml_values[$k];
					break;
				}
			}
		}

		$path = array ();

		if ($node) {
			for($level = $node['level']; $k > - 1; $k --) {
				if ($xml_values[$k]['level'] == $level) {
					array_unshift($path, $xml_values[$k]);
					$level --;
				}
			}
			unset($xml_values);
			return $path;
		} else {
			unset($xml_values);
			return false;
		}
	}

	function arrayToXml($data, $root = 'root'){
		$xml = '<' . $root . '>';
		$this->_arrayToXml($data, $xml);
		$xml .= '</' . $root . '>';
		return $xml;
	}

	function _arrayToXml(&$data, &$xml){
		if (is_array($data)) {
			foreach ($data as $k => $v) {
				if (is_numeric($k)) {
					$xml .= '<item>';
					$xml .= $this->_arrayToXml($v, $xml);
					$xml .= '</item>';
				} else {
					$xml .= '<' . $k . '>';
					$xml .= $this->_arrayToXml($v, $xml);
					$xml .= '</' . $k . '>';
				}
			}
		} elseif (is_numeric($data)) {
			$xml .= $data;
		} elseif (is_string($data)) {
			$xml .= '<![CDATA[' . $data . ']]>';
		}
	}

	function isnumericArray($array){
		if (count($array) > 0 && ! empty($array[0]))
			return true;
		else
			return false;
	}

	function array_xml($keytag, $array){
		$attributes = "";
		$tagcontent = "";

		if (is_array($array)) {
			foreach ($array as $key => $value) {
				if (in_array($key, $member_element[$keytag]) && ! is_array($value)) {
					$attributes .= "$key=\"$value\" ";
				} else if (is_array($value)) {
					if ($this->isnumericArray($value)) {
						for($i = 0; $i < count($value); $i ++) {
							$tagcontent .= $this->array_xml($key, $value[$i]);
						}
					} else
						$tagcontent .= $this->array_xml($key, $value);
				} else if ($key == "value")
					$tagcontent .= $value;
				else
					$tagcontent .= "<{$key}>$value</{$key}>";
			}
		}

		return "<$keytag $attributes>$tagcontent</$keytag>";
	}
}

?>