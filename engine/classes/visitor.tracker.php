<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class VisitorTracker {

    private $core;

    /*
     * Don't count hits from search robots and crawlers. 
     */
    private $IGNORE_SEARCH_BOTS = TRUE;

    /*
     * Which request methods are allowed.
     */
    private $ALLOW_METHODS = array(
        'GET'
    );

    /*
     * ignore controllers e.g. 'admin'
     */
    private $CONTROLLER_IGNORE_LIST = array(
        'admin'
    );

    /*
     * ignore ip address
     */
    private $IP_IGNORE_LIST = array(
        '127.0.0.1'
    );

    function __construct()
    {
        $this->core = & get_instance();
    }

    public function track()
    {
        $proceed = TRUE;

        if ($this->IGNORE_SEARCH_BOTS && $this->is_search_bot())
        {
            $proceed = FALSE;
        }

        if (!in_array($_SERVER['REQUEST_METHOD'], $this->ALLOW_METHODS))
        {
            $proceed = FALSE;
        }

        foreach ($this->CONTROLLER_IGNORE_LIST as $controller)
        {
            if (strpos(trim($this->core->controller), $controller) !== FALSE)
            {
                $proceed = FALSE;
                break;
            }
        }

        if (in_array($this->core->security->getip(), $this->IP_IGNORE_LIST))
        {
            $proceed = FALSE;
        }

        if ($proceed === TRUE)
        {
            $this->log_visitor();
        }
    }

    private function log_visitor()
    {
        $visitor_id = isset($_SESSION['visitor_id']) ? (int)$_SESSION['visitor_id'] : false;
        $page_name = strtolower($this->core->controller) . '/' . $this->core->method;
        $query_string = '';
        if (strpos(uri_string(), '?') !== false)
        {
            $query_string = trim(substr(uri_string(), strpos(uri_string(), '?')));
        }
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        
        if ($visitor_id)
        {
            //update the visitor log in the database, based on the current visitor
            $page_views = isset($_SESSION['page_views']) ? (int)$_SESSION['page_views'] + 1 : 1;
            $current_page = current_url();
            $temp_current_page = isset($_SESSION['current_page']) ? $_SESSION['current_page'] : false;
            
            if ($temp_current_page && $temp_current_page != $current_page)
            {
                $update = $this->core->db->prepare("UPDATE `site_visitors` SET `page_views` = ?, `ip_address` = ?, `requested_url` = ?, `referer_page` = ?, `user_agent` = ?, `page_name` = ?, `query_string` = ? WHERE `visitor_id` = ? LIMIT 1;");
                $update->execute(array(
                    $page_views,
                    $this->core->security->getip(),
                    uri_string(),
                    $referer,
                    $_SERVER['HTTP_USER_AGENT'],
                    $page_name,
                    $query_string,
                    $visitor_id
                ));

                $_SESSION['page_views'] = $page_views;
                $_SESSION['current_page'] = $current_page;
            }
        }
        else
        {
            $insert = $this->core->db->prepare("INSERT INTO `site_visitors` (`page_views`, `ip_address`, `requested_url`, `referer_page`, `user_agent`, `page_name`, `query_string`) VALUES (1, ?, ?, ?, ?, ?, ?);");
            $insert->execute(array(
                $this->core->security->getip(),
                uri_string(),
                $referer,
                $_SERVER['HTTP_USER_AGENT'],
                $page_name,
                $query_string
            ));

            if ($insert->rowCount() > 0)
            {
                $entry_id = $this->core->db->lastInsertId();
                $_SESSION['visitor_id'] = $entry_id;
                $_SESSION['page_views'] = 1;
                $_SESSION['current_page'] = current_url();
            }
        }
    }

    private function is_search_bot() 
    {
        // Of course, this is not perfect, but it at least catches the major
        // search engines that index most often.
        $spiders = array(
            "abot",
            "dbot",
            "ebot",
            "hbot",
            "kbot",
            "lbot",
            "mbot",
            "nbot",
            "obot",
            "pbot",
            "rbot",
            "sbot",
            "tbot",
            "vbot",
            "ybot",
            "zbot",
            "bot.",
            "bot/",
            "_bot",
            ".bot",
            "/bot",
            "-bot",
            ":bot",
            "(bot",
            "crawl",
            "slurp",
            "spider",
            "seek",
            "accoona",
            "acoon",
            "adressendeutschland",
            "ah-ha.com",
            "ahoy",
            "altavista",
            "ananzi",
            "anthill",
            "appie",
            "arachnophilia",
            "arale",
            "araneo",
            "aranha",
            "architext",
            "aretha",
            "arks",
            "asterias",
            "atlocal",
            "atn",
            "atomz",
            "augurfind",
            "backrub",
            "bannana_bot",
            "baypup",
            "bdfetch",
            "big brother",
            "biglotron",
            "bjaaland",
            "blackwidow",
            "blaiz",
            "blog",
            "blo.",
            "bloodhound",
            "boitho",
            "booch",
            "bradley",
            "butterfly",
            "calif",
            "cassandra",
            "ccubee",
            "cfetch",
            "charlotte",
            "churl",
            "cienciaficcion",
            "cmc",
            "collective",
            "comagent",
            "combine",
            "computingsite",
            "csci",
            "curl",
            "cusco",
            "daumoa",
            "deepindex",
            "delorie",
            "depspid",
            "deweb",
            "die blinde kuh",
            "digger",
            "ditto",
            "dmoz",
            "docomo",
            "download express",
            "dtaagent",
            "dwcp",
            "ebiness",
            "ebingbong",
            "e-collector",
            "ejupiter",
            "emacs-w3 search engine",
            "esther",
            "evliya celebi",
            "ezresult",
            "falcon",
            "felix ide",
            "ferret",
            "fetchrover",
            "fido",
            "findlinks",
            "fireball",
            "fish search",
            "fouineur",
            "funnelweb",
            "gazz",
            "gcreep",
            "genieknows",
            "getterroboplus",
            "geturl",
            "glx",
            "goforit",
            "golem",
            "grabber",
            "grapnel",
            "gralon",
            "griffon",
            "gromit",
            "grub",
            "gulliver",
            "hamahakki",
            "harvest",
            "havindex",
            "helix",
            "heritrix",
            "hku www octopus",
            "homerweb",
            "htdig",
            "html index",
            "html_analyzer",
            "htmlgobble",
            "hubater",
            "hyper-decontextualizer",
            "ia_archiver",
            "ibm_planetwide",
            "ichiro",
            "iconsurf",
            "iltrovatore",
            "image.kapsi.net",
            "imagelock",
            "incywincy",
            "indexer",
            "infobee",
            "informant",
            "ingrid",
            "inktomisearch.com",
            "inspector web",
            "intelliagent",
            "internet shinchakubin",
            "ip3000",
            "iron33",
            "israeli-search",
            "ivia",
            "jack",
            "jakarta",
            "javabee",
            "jetbot",
            "jumpstation",
            "katipo",
            "kdd-explorer",
            "kilroy",
            "knowledge",
            "kototoi",
            "kretrieve",
            "labelgrabber",
            "lachesis",
            "larbin",
            "legs",
            "libwww",
            "linkalarm",
            "link validator",
            "linkscan",
            "lockon",
            "lwp",
            "lycos",
            "magpie",
            "mantraagent",
            "mapoftheinternet",
            "marvin/",
            "mattie",
            "mediafox",
            "mediapartners",
            "mercator",
            "merzscope",
            "microsoft url control",
            "minirank",
            "miva",
            "mj12",
            "mnogosearch",
            "moget",
            "monster",
            "moose",
            "motor",
            "multitext",
            "muncher",
            "muscatferret",
            "mwd.search",
            "myweb",
            "najdi",
            "nameprotect",
            "nationaldirectory",
            "nazilla",
            "ncsa beta",
            "nec-meshexplorer",
            "nederland.zoek",
            "netcarta webmap engine",
            "netmechanic",
            "netresearchserver",
            "netscoop",
            "newscan-online",
            "nhse",
            "nokia6682/",
            "nomad",
            "noyona",
            "nutch",
            "nzexplorer",
            "objectssearch",
            "occam",
            "omni",
            "open text",
            "openfind",
            "openintelligencedata",
            "orb search",
            "osis-project",
            "pack rat",
            "pageboy",
            "pagebull",
            "page_verifier",
            "panscient",
            "parasite",
            "partnersite",
            "patric",
            "pear.",
            "pegasus",
            "peregrinator",
            "pgp key agent",
            "phantom",
            "phpdig",
            "picosearch",
            "piltdownman",
            "pimptrain",
            "pinpoint",
            "pioneer",
            "piranha",
            "plumtreewebaccessor",
            "pogodak",
            "poirot",
            "pompos",
            "poppelsdorf",
            "poppi",
            "popular iconoclast",
            "psycheclone",
            "publisher",
            "python",
            "rambler",
            "raven search",
            "roach",
            "road runner",
            "roadhouse",
            "robbie",
            "robofox",
            "robozilla",
            "rules",
            "salty",
            "sbider",
            "scooter",
            "scoutjet",
            "scrubby",
            "search.",
            "searchprocess",
            "semanticdiscovery",
            "senrigan",
            "sg-scout",
            "shai'hulud",
            "shark",
            "shopwiki",
            "sidewinder",
            "sift",
            "silk",
            "simmany",
            "site searcher",
            "site valet",
            "sitetech-rover",
            "skymob.com",
            "sleek",
            "smartwit",
            "sna-",
            "snappy",
            "snooper",
            "sohu",
            "speedfind",
            "sphere",
            "sphider",
            "spinner",
            "spyder",
            "steeler/",
            "suke",
            "suntek",
            "supersnooper",
            "surfnomore",
            "sven",
            "sygol",
            "szukacz",
            "tach black widow",
            "tarantula",
            "templeton",
            "/teoma",
            "t-h-u-n-d-e-r-s-t-o-n-e",
            "theophrastus",
            "titan",
            "titin",
            "tkwww",
            "toutatis",
            "t-rex",
            "tutorgig",
            "twiceler",
            "twisted",
            "ucsd",
            "udmsearch",
            "url check",
            "updated",
            "vagabondo",
            "valkyrie",
            "verticrawl",
            "victoria",
            "vision-search",
            "volcano",
            "voyager/",
            "voyager-hc",
            "w3c_validator",
            "w3m2",
            "w3mir",
            "walker",
            "wallpaper",
            "wanderer",
            "wauuu",
            "wavefire",
            "web core",
            "web hopper",
            "web wombat",
            "webbandit",
            "webcatcher",
            "webcopy",
            "webfoot",
            "weblayers",
            "weblinker",
            "weblog monitor",
            "webmirror",
            "webmonkey",
            "webquest",
            "webreaper",
            "websitepulse",
            "websnarf",
            "webstolperer",
            "webvac",
            "webwalk",
            "webwatch",
            "webwombat",
            "webzinger",
            "wget",
            "whizbang",
            "whowhere",
            "wild ferret",
            "worldlight",
            "wwwc",
            "wwwster",
            "xenu",
            "xget",
            "xift",
            "xirq",
            "yandex",
            "yanga",
            "yeti",
            "yodao",
            "zao/",
            "zippp",
            "zyborg"
        );

        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);

        foreach ($spiders as $spider) {
            if (strpos($agent, $spider) !== FALSE)
                return TRUE;
        }

        return FALSE;
    }
}