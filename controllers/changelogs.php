<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Changelogs extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();

        $this->loadConfig('changelogs');
    }
    
    public function index()
    {
        $repoId = isset($_GET['repo']) ? $_GET['repo'] : 'web';
        if ($repoId)
            $repoId = filter_var($repoId, FILTER_SANITIZE_STRING);

        $perPage = max(1, min(30, CHANGELOG_PERPAGE));
        $reposConfig = $this->configItem('repos', 'changelogs');

        if ($repoId === false || !isset($reposConfig[$repoId]))
        {
            $this->tpl->Message('Changelogs', 'An error occured!', 'Invalid repository identifier!');
        }

        // Get the commits
        $commits = $this->getCommits($repoId, 1);

        $this->tpl->SetTitle('Changelogs');
        $this->tpl->SetSubtitle('Changelogs');
        $this->tpl->AddCSS('template/style/page-changelogs.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('changelogs', array(
            'repoId' => $repoId,
            'repos' => $reposConfig,
            'perPage' => $perPage,
            'commits' => $commits,
            'loadMore' => ($commits && count($commits) >= $perPage)
        ));

        $this->tpl->LoadFooter();
    }

    public function get_changesets()
    {
        $page = ((isset($_GET['page'])) ? (int)$_GET['page'] : 1);
        $repoId = isset($_GET['repo']) ? $_GET['repo'] : 'web';
        if ($repoId)
            $repoId = filter_var($repoId, FILTER_SANITIZE_STRING);

        $reposConfig = $this->configItem('repos', 'changelogs');

        if ($repoId === false || !isset($reposConfig[$repoId]))
        {
            $this->JsonError('Invalid repository identifier!');
        }

        // Get the commits
        $commits = $this->getCommits($repoId, $page);
        
        if (!headers_sent())
        {
            header("content-type: application/json");
        }

        $this->Json($commits);
    }

    private function getCommits($repoId, $page = 1)
    {
        $perPage = max(1, min(30, CHANGELOG_PERPAGE));

        $reposConfig = $this->configItem('repos', 'changelogs');

        if ($repoId === false || !isset($reposConfig[$repoId]))
        {
            return false;
        }

        // Do some cache to speed up load times
        $cache = $this->cache->get('changelogs/'.$repoId.'_'.$page);

        if ($cache !== false)
		{
			return $cache;
		}
		else
		{
            $token = $reposConfig[$repoId]['token'];
            $owner = $reposConfig[$repoId]['owner'];
            $reponame = $reposConfig[$repoId]['repo'];
            $branch = $reposConfig[$repoId]['branch'];

            $this->loadLibrary('github.client');

            $client = new GitHubClient();
            $client->setAuthType(GitHubClient::GITHUB_AUTH_TYPE_OAUTH);
            $client->setOauthToken($token);
            $client->setPage($page);
            $client->setPageSize($perPage);
            $commitsRaw = $client->repos->commits->listCommitsOnRepository($owner, $reponame, $branch);

            $commits = array();

            if ($commitsRaw && !empty($commitsRaw))
            {
                foreach ($commitsRaw as $commit)
                {
                    $date = $commit->getCommit()->getAuthor()->getDate();
                    $time = $this->getTime(true, $date);
                    $time = $time->format('j M H:i');

                    $commits[] = array(
                        'rev' => mb_strimwidth($commit->getSha(), 0, 8),
                        'text' => $commit->getCommit()->getMessage(),
                        'author' => $commit->getCommit()->getAuthor()->getName(),
                        'time' => $time,
                    );
                }
            }

            // Cache for 10 min
            $this->cache->store('changelogs/'.$repoId.'_'.$page, $commits, 10 * 60);

            return $commits;
        }
    }
}