<?php 
if (!defined('init_engine'))
{
    header('HTTP/1.0 404 not found');
    exit;
}

require_once ROOTPATH . '/engine/admin_controller.php';

class Tickets extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->CheckPermission(PERMISSION_TICKETS);
    }

    public function index()
    {
        $RealmID = isset($_GET['realm']) ? (int)$_GET['realm'] : false;
        $iclosed = isset($_GET['iclosed']) ? (int)$_GET['iclosed'] : 0;

        //Save selected realm in a session
        if ($RealmID !== false)
        {
            $_SESSION['ADMIN_SelectedRealm'] = $this->realms->realmExists($RealmID) ? $RealmID : 1;
        }
        else
        {
            $RealmID = isset($_SESSION['ADMIN_SelectedRealm']) ? (int)$_SESSION['ADMIN_SelectedRealm'] : 1;
        }

        $realm = $this->realms->getRealm($RealmID);

        //Try the realm
        if (($REALM_DB = $realm->getCharactersConnection()) === false)
        {
            $this->ErrorBox('Unable to connect to the realm database.');
        }

        $table = $realm->getCharacters()->getTable('gm_tickets');
        $charsTable = $realm->getCharacters()->getTable('characters');
        $columns = $realm->getCharacters()->getAllColumns('gm_tickets');
        $charsColumns = $realm->getCharacters()->getAllColumns('characters');

        if ($iclosed == 1)
        {
            $where = "";
        }
        else
        {
            $where = "WHERE `".$columns['closedBy']."` = '0'";
        }
        
        $tickets = false;
        $res = $REALM_DB->prepare("SELECT 
                                    `".$columns['ticketId']."` AS `ticketId`, 
                                    `".$columns['message']."` AS `message`, 
                                    `".$columns['guid']."` AS `guid`, 
                                    `".$columns['name']."` AS `name`, 
                                    `".$columns['createTime']."` AS `createTime`, 
                                    `".$columns['closedBy']."` AS `closedBy`, 
                                    `".$columns['assignedTo']."` AS `assignedTo`, 
                                    `".$columns['comment']."` AS `comment`, 
                                    `".$columns['viewed']."` AS `viewed` 
                            FROM `".$table."` ".$where."
                            ORDER BY `".$columns['createTime']."` DESC;");
        $res->execute();

        if ($res->rowCount() > 0)
        {
            $tickets = $res->fetchAll();

            foreach ($tickets as $i => $arr)
            {
                if ((int)$arr['closedBy'] == 0)
                {
                    $tickets[$i]['status'] = 'Open';
                    $tickets[$i]['online'] = '[offline]';

                    //Check if the user is online
                    $res2 = $REALM_DB->prepare("SELECT `".$charsColumns['online']."` AS `online` FROM `".$charsTable."` WHERE `".$charsColumns['guid']."` = :guid LIMIT 1;");
                    $res2->bindParam(':guid', $arr['guid'], PDO::PARAM_INT);
                    $res2->execute();
                    
                    if ($res2->rowCount() > 0)
                    {
                        $char = $res2->fetch();
                        if ((int)$char['online'] == 1)
                        {
                            $tickets[$i]['online'] = '[online]';
                        }
                        unset($char);
                    }
                    unset($res2);
                }
                else
                {
                    $tickets[$i]['status'] = 'Closed';
                }
            }
        }

        //Print the page
        $this->PrintPage('tickets', array(
            'RealmID' => $RealmID,
            'iclosed' => $iclosed,
            'tickets' => $tickets
        ));
    }
}