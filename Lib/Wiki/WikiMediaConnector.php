<?php
/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Wiki;

use Eulogix\Cool\Lib\Traits\CoolCacheShimmed;
use Eulogix\Lib\Cache\Shimmable;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class WikiMediaConnector implements Shimmable
{
    use CoolCacheShimmed;

    /**
     * @var string
     */
    private $api, $articleBase, $user, $password;

    /**
     * @var \Wikimate
     */
    private $wiki;

    /**
     * @param string $api
     * @param string $articleBase
     * @param string $user
     * @param string $password
     */
    public function __construct($api, $articleBase, $user, $password) {
        $this->api = $api;
        $this->articleBase = $articleBase;
        $this->user = $user;
        $this->password = $password;

        $this->wiki = new \Wikimate($api);
    }

    /**
     * @return string
     */
    public function getShimUID() {
        return md5($this->api);
    }

    /**
     * @param $pageTitle
     * @return bool
     */
    public function isPageTitleValid($pageTitle) {
        return !preg_match('/[\[\]\{\}\-]+/sim', $pageTitle);
    }

    /**
     * @param $processedTitle
     * @return \WikiPage
     */
    public function getPage($processedTitle)
    {
        if( $r = $this->getShim()->callMethod(__METHOD__, func_get_args())) return $r;

        $this->lazyLogin();
        return $this->wiki->getPage($processedTitle);
    }

    /**
     * @param string $wikiText
     * @return string
     */
    public function parseAsHTML($wikiText) {
        if( $r = $this->getShim()->callMethod(__METHOD__, func_get_args())) return $r;
        $encodedText = urlencode($wikiText);
        $apiUrl = "{$this->api}/w/api.php?action=parse&text={$encodedText}&contentmodel=wikitext&format=json";
        if($content = file_get_contents($apiUrl)) {
            $output = json_decode($content, true);
            return @$output['parse']['text']['*'];
        }
    }

    /**
     * @param $wikiPage
     * @return string
     */
    public function getCompleteUrlForPage($wikiPage) {
        return $this->articleBase.$wikiPage;
    }

    /**
     * TODO implement the actual lazy part, but I'm lazy and for now the shim will do
     * @throws \Exception
     */
    private function lazyLogin() {
        if(!$this->wiki->login($this->user, $this->password)) {
            throw new \Exception("Failed to connect to {$this->api}");
        }
    }

}