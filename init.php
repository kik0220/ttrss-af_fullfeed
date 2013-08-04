<?php
/*
 * af_fullfeed/init.php
 * Plugin for TT-RSS 1.9
 *
 * This is af_newspapers's fork.
 * This plugin get japanese well known site's article.
 *
 * CHANGELOG:
 * Version 1.1 by kik0220 2013-08-05 @ 08:16 JST
 * 	- Add some sites.
 * Version 1.0 by kik0220 2013-05-13 @ 20:11 JST
 * 	- Initial release
 */
class Af_fullfeed extends Plugin {
	private $host;

	function about() {
		return array(1.1,
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
          array('link'=>"asahi.com"           ,'xpath'=>'(//div[@class="BodyTxt"])'),
          array('link'=>"livedoor.com"        ,'xpath'=>'(//div[@id="main"])'),
          array('link'=>"hatena.ne.jp"        ,'xpath'=>'(//div[@id="days"])'),
          array('link'=>"nikkeibp.co.jp"      ,'xpath'=>'(//div[@id="kijiBox"])'),
          array('link'=>"nikkeibp.co.jp"      ,'xpath'=>'(//div[@id="articlebody"])'),
          array('link'=>"rbbtoday.com"        ,'xpath'=>'(//div[@id="articleItem"])'),
          array('link'=>"impress.co.jp"       ,'xpath'=>'(//div[@class="main-contents mainContents column stapablog column-stapablog"])'), //stapablog
          array('link'=>"infoq.com"           ,'xpath'=>'(//div[@id="content"])'),
          array('link'=>"thinkit.co.jp"       ,'xpath'=>'(//div[@class="content_body"])'),
          array('link'=>"itmedia.co.jp"       ,'xpath'=>'(//div[@id="blogBody"])'),
          array('link'=>"jigokuno.com"        ,'xpath'=>'(//div[@id="main"])'),
          array('link'=>"publickey1.jp"       ,'xpath'=>'(//div[@id="maincol"])'),
          array('link'=>"phpspot.org"         ,'xpath'=>'(//div[@class="entrybody"])'),
          array('link'=>"juggly.cn"           ,'xpath'=>'(//div[@id="content"])'),
          array('link'=>"wnyan.jp"            ,'xpath'=>'(//div[@id="main"])'),
          array('link'=>"agilecatcloud.com"   ,'xpath'=>'(//div[@id="content"])'),
          array('link'=>"feedproxy.google.com",'xpath'=>'(//div[@id="main"])'),
          array('link'=>"feedproxy.google.com",'xpath'=>'(//div[@class="post hentry"])'),
          array('link'=>"rss.rssad.jp"        ,'xpath'=>'(//div[@class="main-contents mainContents"])'), //impress.co.jp
          array('link'=>"rss.rssad.jp"        ,'xpath'=>'(//div[@id="cmsBody"]/div[@class="inner"])'), //itmedia.co.jp
          array('link'=>"rss.rssad.jp"        ,'xpath'=>'(//div[@id="primary"])'), //gihyo.jp
          array('link'=>"rss.rssad.jp"        ,'xpath'=>'(//div[@id="main"])'), //codezine.jp
          array('link'=>"rss.rssad.jp"        ,'xpath'=>'(//div[@id="cmsBody"])') //atmarkit.co.jp
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
