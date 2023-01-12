<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Unstuck extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();

        $this->loggedInOrReturn();
    }
    
    public function index()
    {
        //Set the title
        $this->tpl->SetTitle('Unstuck');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCSS('template/style/page-unstuck.css');

        //Print the header
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('unstuck');

        //print the footer
        $this->tpl->LoadFooter();
    }

    public function submit()
    {
        //prepare multi errors
        $this->errors->NewInstance('unstuck');

        $RealmId = $this->user->GetRealmId();

        $charName = (isset($_POST['character']) ? $_POST['character'] : false);

        //bind the onsuccess message
        $this->errors->onSuccess($charName . ' has been successfully unstucked.', '/unstuck');

        $cooldown = $this->user->getCooldown('unstuck');
        $cooldownTime = '15 minutes';

        if (!$charName)
        {
            $this->errors->Add("Please select a character.");
        }

        if (!$RealmId)
        {
            $this->errors->Add("There is no realm assigned to your account.");
        }

        //check if realm exists
        if (!$this->realms->realmExists($RealmId))
        {
            $this->errors->Add("The selected realm is invalid.");
        }

        $realm = $this->realms->getRealm($RealmId);

        //check if the characters database is reachable
        if (!$realm->checkCharactersConnection())
        {
            $this->errors->Add("The website failed to load realm database. Please contact the administration for more information.");
        }

        //check if this character belongs to this account
        if (!$realm->getCharacters()->isMyCharacter(false, $charName))
        {
            $this->errors->Add('The selected character does not belong to this account.');
        }
        
        //check the cooldown
        if (time() < $cooldown)
        {
            $this->errors->Add('This tool is on cooldown, please try again later.');
        }

        $this->errors->Check('/unstuck');

        //try unsticking
        $unstuck = $realm->getCharacters()->Unstuck(false, $charName);
        
        //update the cooldown if the character was unstucked
        if ($unstuck)
        {
            //set cooldown because we got no errors
            $this->user->setCooldown('unstuck', strtotime('+'.$cooldownTime));

            //redirect
            $this->errors->triggerSuccess();
        }
        else
        {
            $this->errors->Add('The website failed to unstuck your character. Please try again later or contact the administration.');
        }
        
        $this->errors->Check('/unstuck');
        exit;
    }
}