<?php
/*
 * af_fullfeed/init.php
 * Plugin for TT-RSS 1.7.9
 *
 * This is af_newspapers's fork.
 * This plugin get japanese well known site's article.
 *
 * CHANGELOG:
 * Version 1.0 by kik0220 2013-05-13 @ 20:11 JST
 * 	- Initial release
 */
class Af_fullfeed extends Plugin {
	private $host;

	function about() {
		return array(1.0,
			"This is af_newspapers's fork.This plugin get japanese well known site's article.",
			"kik0220");
	}

	function init($host) {
		$this->host = $host;

		$host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
	}

	function hook_article_filter($article) {
		$owner_uid = $article["owner_uid"];
        $targets = array(
             array('link'=>"asahi.com"    ,'xpath'=>'(//div[@class="BodyTxt"])') //'(//div[@id="MainInner"])'
            ,array('link'=>"rbbtoday.com" ,'xpath'=>'(//div[@id="articleItem"])')
            ,array('link'=>"rss.rssad.jp",'xpath'=>'(//div[@class="main-contents mainContents"])') //impress.co.jp
            ,array('link'=>"rss.rssad.jp",'xpath'=>'(//div[@id="cmsBody"]/div[@class="inner"])') //itmedia.co.jp
            ,array('link'=>"livedoor.com" ,'xpath'=>'(//div[@id="main"])')
        );

        foreach ( $targets as $target) {
			if (strpos($article["guid"], $target["link"]) === FALSE) {
			    continue;
            }
			if (strpos($article["plugin_data"], "fullfeed,$owner_uid:") === FALSE) {
				$doc = new DOMDocument();
				@$doc->loadHTML(fetch_file_contents($article["link"]));
				$basenode = false;

				if ($doc) {
					$xpath = new DOMXPath($doc);
					$entries = $xpath->query($target["xpath"]);
					foreach ($entries as $entry) {
						$basenode = $entry;
					}
					if ($basenode) {
						$article["content"] = $doc->saveXML($basenode); //, LIBXML_NOEMPTYTAG);
						$article["plugin_data"] = "fullfeed,$owner_uid:" . $article["plugin_data"];
					}
				}
			} else if (isset($article["stored"]["content"])) {
				$article["content"] = $article["stored"]["content"];
			}
		}
		return $article;
	}

	function api_version() {
		return 2;
	}
}
?>
