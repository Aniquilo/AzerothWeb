<?php

require_once(__DIR__ . '/../GitHubObject.php');

	

class GitHubGistHistoryHistory extends GitHubObject
{
	/* (non-PHPdoc)
	 * @see GitHubObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
		));
	}
	
}

