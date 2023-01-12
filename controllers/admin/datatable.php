<?php 
if (!defined('init_engine'))
{
    header('HTTP/1.0 404 not found');
    exit;
}

require_once ROOTPATH . '/engine/admin_controller.php';

class Datatable extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function articles()
    {
        $this->CheckPermissionSilent(PERMISSION_ARTICLES);

        $aColumns = array('id', 'title', 'short_text', 'views', 'added', 'author', 'image');
        $sIndexColumn = "id";
        $sTable = 'articles';
        $sAddWhere = false;

        $this->get_data($aColumns, $sIndexColumn, $sTable, $sAddWhere, function ($aRow)
        {
            //find the account
            $res = $this->db->prepare("SELECT displayName FROM `account_data` WHERE `id` = :id LIMIT 1;");
            $res->bindParam(':id', $aRow['author'], PDO::PARAM_INT);
            $res->execute();

            //if we found it
            if ($res->rowCount() > 0)
            {
                $row = $res->fetch();
                $aRow['author_str'] = $row['displayName'];
                unset($row);
            }
            else
            {
                $aRow['author_str'] = 'Unknown';
            }
            unset($res);
            
            //Set the first two columns
            $row = array();
            $row[0] = $aRow['id'];
            $row[1] = htmlspecialchars(stripslashes($aRow['title']));
            $row[2] = '<div style="width: 500px;">' . htmlspecialchars(stripslashes($aRow['short_text'])) . '</div>';
            $row[3] = $aRow['views'];
            $row[4] = $aRow['added'];
            $row[5] = '<a href="'.base_url().'/admin/user-preview?uid='.$aRow['author'].'">' . $aRow['author_str'] . '</a> [' . $aRow['author'] . ']';
            $row[6] = '<span class="button-group">'.
                        '<a href="'.base_url().'/admin/articles/edit?id='.$aRow['id'].'" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</a> '.
                        '<a href="'.base_url().'/admin/articles/delete?id='.$aRow['id'].'" class="btn btn-danger btn-sm" data-confirm="Are you sure you want to delete this article?"><i class="fas fa-trash"></i> Delete</a>'.
                      '</span>';
            
            return $row;
        });
    }

    public function store_items()
    {
        $this->CheckPermissionSilent(PERMISSION_STORE);

        $aColumns = array('entry', 'name', 'ItemLevel', 'realm', 'gold', 'silver', 'class', 'subclass', 'Quality', 'id');
        $sIndexColumn = "id";
        $sTable = 'store_items';
        $sAddWhere = false;

        if (isset($_GET['realm']) && $_GET['realm'] != '-1')
        {
            $sAddWhere = "`realm` LIKE CONCAT('%', '".(int)$_GET['realm']."', '%')";
        }
        
        $this->get_data($aColumns, $sIndexColumn, $sTable, $sAddWhere, function ($aRow)
        {
            $realms = explode(',', $aRow['realm']);
		
            //Set the first two columns
            $row = array();
            $row[0] = $aRow['entry'];
            $row[1] = '<a href="" class="q'.$aRow['Quality'].' item-link" rel="item='.$aRow['entry'].'" data-realm="'.$realms[key($realms)].'">' . $aRow['name'] . '</a>';
            $row[2] = $aRow['ItemLevel'];
            $row[3] = $aRow['realm'];
            $row[4] = $aRow['gold'];
            $row[5] = $aRow['silver'];
            $row[6] = Item_FindClass($aRow['class']) . ' [' . $aRow['class'] . ']';
            $row[7] = Item_FindSubclass($aRow['class'], $aRow['subclass']) . ' [' . $aRow['subclass'] . ']';
            $row[8] = '<span class="button-group">
                        <a href="#" data-id="'.$aRow['id'].'" class="btn btn-primary btn-sm btn-edit"><i class="fas fa-edit"></i> Edit</a> 
                        <a href="#" data-id="'.$aRow['id'].'" class="btn btn-danger btn-sm btn-delete"><i class="fas fa-trash"></i> Delete</a>
                    </span>';
            
            return $row;
        });
    }

    public function promo_codes()
    {
        $this->CheckPermissionSilent(PERMISSION_PROMO_CODES);

        $aColumns = array('id', 'token', 'usage', 'reward_type', 'reward_value', 'format', 'added');
        $sIndexColumn = "id";
        $sTable = 'promo_codes';
        $sAddWhere = false;

        $this->get_data($aColumns, $sIndexColumn, $sTable, $sAddWhere, function ($aRow)
        {
            //Resolve the usage type
            switch ((int)$aRow['usage'])
            {
                case PCODE_USAGE_ONCE:
                    $usage = 'Unique';
                    break;
                case PCODE_USAGE_PER_ACC:
                    $usage = 'Per Account';
                    $usageRes = $this->db->prepare("SELECT COUNT(*) AS usages FROM `promo_codes_usage` WHERE `token` = :token;");
                    $usageRes->bindParam(':token', $aRow['token'], PDO::PARAM_STR);
                    $usageRes->execute();
                    $row = $usageRes->fetch();
                    $usage .= ' (Used ' . $row['usages'] . ' times)';
                    unset($usageRes, $row);
                    break;
                default:
                    $usage = 'Unknown';
                    break;
            }
            
            //Resolve the reward
            switch ((int)$aRow['reward_type'])
            {
                case PCODE_REWARD_CURRENCY_S:
                    $reward = $aRow['reward_value'] . ' Silver Coins';
                    break;
                case PCODE_REWARD_CURRENCY_G:
                    $reward = $aRow['reward_value'] . ' Gold Coins';
                    break;
                case PCODE_REWARD_ITEM:
                    $reward = 'Item: ' . $aRow['reward_value'];
                    break;
            }
            
            //Set the first two columns
            $row = array();
            $row[0] = $aRow['id'];
            $row[1] = $this->FormatCode($aRow['token'], $aRow['format']);
            $row[2] = $usage;
            $row[3] = $reward;
            $row[4] = $aRow['added'];
            $row[5] = '<a href="'.base_url().'/admin/pcodes/delete?id='.$aRow['id'].'" class="btn btn-danger btn-sm" data-confirm="Are you sure you want to delete this code?"><i class="fas fa-trash"></i> Delete</a>';
                
            return $row;
        });
    }

    private function FormatCode($token, $format)
	{
		//split into markers
		$markers = str_split($format);
		$keyChar = str_split($token);
		
		$reduce = 0;
		$key = '';
		//let's put up our key
		foreach ($markers as $index => $marker)
		{
			if (strtolower($marker) == 'x')
			{
				$key .= $keyChar[$index - $reduce];
			}
			else
			{
				$key .= $markers[$index];
				$reduce++;
			}
		}
		unset($markers, $keyChar, $index, $marker, $reduce);
		
		return $key;
    }
    
    public function users()
    {
        $this->CheckPermissionSilent(PERMISSION_PREV_USERS);

        $aColumns = array('id', 'displayName', 'rank', 'reg_ip');
        $sIndexColumn = "id";
        $sTable = 'account_data';
        $sAddWhere = false;

        $this->get_data($aColumns, $sIndexColumn, $sTable, $sAddWhere, function ($aRow)
        {
            //Pull some data from the Auth DB
            $accColumns = $this->authentication->getAllColumns('account');
            $authRes = $this->auth_db->prepare("SELECT 
                    `".$accColumns['username']."` AS username, 
                    `".$accColumns['email']."` AS email, 
                    `".$accColumns['joindate']."` AS joindate, 
                    `".$accColumns['last_ip']."` AS last_ip 
                FROM `".$this->authentication->getTable('account')."` 
                WHERE `".$accColumns['id']."` = :acc LIMIT 1;");
            $authRes->bindParam(':acc', $aRow['id'], PDO::PARAM_INT);
            $authRes->execute();
            $authRow = $authRes->fetch();
            unset($accColumns);
            
            $GMLevel = '';
            $gmColumns = $this->authentication->getAllColumns('account_access');
            $gmRes = $this->auth_db->prepare("SELECT `".$gmColumns['gmlevel']."` AS gmlevel, `".$gmColumns['realmid']."` AS realmid FROM `".$this->authentication->getTable('account_access')."` WHERE `".$gmColumns['id']."` = :acc;");
            $gmRes->bindParam(':acc', $aRow['id'], PDO::PARAM_INT);
            $gmRes->execute();
            if ($gmRes->rowCount() > 0)
            {
                //Loop the records
                while ($gmRec = $gmRes->fetch())
                {
                    $GMLevel .= $gmRec['gmlevel'] . ' - Realm: ' . $gmRec['realmid'] . '<br>';
                }
                //remove the last <br>
                $GMLevel = substr($GMLevel, 0, strlen($GMLevel) - 4);
            }
            unset($gmColumns);

            //Setup the rank
            $Rank = new UserRank($aRow['rank']);
            
            //Set the first two columns
            $row = array();
            $row[0] = $aRow['id'];
            $row[1] = '<a href="'.base_url().'/admin/users/view?uid='.$aRow['id'].'">' . $aRow['displayName'] . '</a> [' . $authRow['username'] . ']';
            $row[2] = $Rank->string() . ' [' . $Rank->int() . ']';
            $row[3] = $GMLevel;
            $row[4] = $authRow['email'];
            $row[5] = $authRow['last_ip'];
            $row[6] = $aRow['reg_ip'];
            $row[7] = $authRow['joindate'];
                
            return $row;
        });
    }

    public function paypal_logs()
    {
        $this->CheckPermissionSilent(PERMISSION_LOGS);

        $aColumns = array('id', 'account', 'txn_id', 'txn_type', 'amount', 'payer_email', 'receiver_email', 'time', 'paypal_status', 'query_string', 'text', 'type');
	    $sIndexColumn = "id";
        $sTable = 'paypal_logs';
        $sAddWhere = false;

        $this->get_data($aColumns, $sIndexColumn, $sTable, $sAddWhere, function ($aRow)
        {
            //find the account
            $res = $this->db->prepare("SELECT displayName FROM `account_data` WHERE `id` = :id LIMIT 1;");
            $res->bindParam(':id', $aRow['account'], PDO::PARAM_INT);
            $res->execute();
            //if we found it
            if ($res->rowCount() > 0)
            {
                $row = $res->fetch();
                $aRow['account'] = '<a href="'.base_url().'/admin/users/view?uid='.$aRow['account'].'">' . $row['displayName'] . '</a> [' . $aRow['account'] . ']';
                unset($row);
            }
            unset($res);
            
            //Let's parse the text
            $Find = array('[Success]', '[Error]');
            $Replace = array('<br><font color="green">Success</font>', '<br><font color="red">Error</font>');
            
            $aRow['text'] = str_replace($Find, $Replace, $aRow['text']);
            
            //Find the first br and remove it if we have one
            if (substr($aRow['text'], 0, 4) == '<br>')
                $aRow['text'] = substr($aRow['text'], 4);
            
            //Translate the log type
            switch ($aRow['type'])
            {
                case TRANSACTION_LOG_TYPE_NONE:
                    $aRow['type'] = 'None';
                    break;
                case TRANSACTION_LOG_TYPE_NORMAL:
                    $aRow['type'] = 'Normal';
                    break;
                case TRANSACTION_LOG_TYPE_URGENT:
                    $aRow['type'] = 'Urgent';
                    break;
            }
            
            //Set the first two columns
            $row = array();
            $row[0] = $aRow['id'];
            $row[1] = $aRow['paypal_status'];
            $row[2] = $aRow['type'];
            $row[3] = '
                <div class="datatable-expander" style="position: relative;">
                    <p style="width: 90%;">
                        '.$aRow['text'].'
                    </p>
                    <span style="position: absolute; top: 1px; right: 0px;">
                        <a href="#" onclick="return ToggleExpand(this);">Open</a>
                    </span>
                    <p id="content" style="display: none">
                        <br />
                        <strong>Contributer Paypal</strong>: '.$aRow['payer_email'].'<br>
                        <strong>Datetime</strong>: '.$aRow['time'].'<br><br>
                        <span style="vertical-align: top; font-weight: bold;">Query: </span>
                        <textarea style="width: 80%;">'.$aRow['query_string'].'</textarea>
                    </p>
                </div>';
            $row[4] = $aRow['txn_id'];
            $row[5] = $aRow['txn_type'];
            $row[6] = $aRow['amount'];
            $row[7] = $aRow['account'];
            
            return $row;
        });
    }

    public function paymentwall_logs()
    {
        $this->CheckPermissionSilent(PERMISSION_LOGS);

        $aColumns = array('id', 'text', 'TransactionQuery', 'TransactionRefId', 'TransactionAmount', 'account');
	    $sIndexColumn = "id";
        $sTable = 'paymentwall_logs';
        $sAddWhere = false;

        $this->get_data($aColumns, $sIndexColumn, $sTable, $sAddWhere, function ($aRow)
        {
            //find the account
            $res = $this->db->prepare("SELECT displayName FROM `account_data` WHERE `id` = :id LIMIT 1;");
            $res->bindParam(':id', $aRow['account'], PDO::PARAM_INT);
            $res->execute();
            //if we found it
            if ($res->rowCount() > 0)
            {
                $row = $res->fetch();
                $aRow['account'] = '<a href="'.base_url().'/admin/users/view?uid='.$aRow['account'].'">' . $row['displayName'] . '</a> [' . $aRow['account'] . ']';
                unset($row);
            }
            unset($res2);
            
            //Set the first two columns
            $row = array();
            $row[0] = $aRow['id'];
            $row[1] = '
                <div class="datatable-expander" style="position: relative;">
                    <p>'.$aRow['text'].'</p>
                    <span style="position: absolute; top: 1px; right: 0px;">
                        <a href="#" onclick="return ToggleExpand(this);">Open</a>
                    </span>
                    <p id="content" style="display: none">
                        <br>Query: '.$aRow['TransactionQuery'].'
                    </p>
                </div>';
            $row[2] = $aRow['TransactionRefId'];
            $row[3] = $aRow['TransactionAmount'];
            $row[4] = $aRow['account'];
            
            return $row;
        });
    }

    public function account_activity()
    {
        $this->CheckPermissionSilent(PERMISSION_PREV_USERS);

        $userId = (isset($_GET['uid']) ? (int)$_GET['uid'] : false);

        $aColumns = array('id', 'description', 'ip_address', 'time');
	    $sIndexColumn = "id";
        $sTable = 'account_activity';
        $sAddWhere = false;

        if ($userId !== false)
        {
            $sAddWhere = "`account` = '".$userId."'";
        }

        $this->get_data($aColumns, $sIndexColumn, $sTable, $sAddWhere, function ($aRow)
        {
            //Set the first two columns
            $row = array();
            $row[0] = $aRow['id'];
            $row[1] = $aRow['description'];
            $row[2] = $aRow['ip_address'];
            $row[3] = $aRow['time'];
            
            return $row;
        });
    }

    public function coins_activity()
    {
        $this->CheckPermissionSilent(PERMISSION_PREV_USERS);

        $userId = (isset($_GET['uid']) ? (int)$_GET['uid'] : false);

        $aColumns = array('id', 'source', 'sourceType', 'coinsType', 'exchangeType', 'amount', 'time');
	    $sIndexColumn = "id";
        $sTable = 'coin_activity';
        $sAddWhere = false;

        if ($userId !== false)
        {
            $sAddWhere = "`account` = '".$userId."'";
        }

        $this->get_data($aColumns, $sIndexColumn, $sTable, $sAddWhere, function ($aRow)
        {
            //check the source type
            switch ($aRow['sourceType'])
            {
                case CA_SOURCE_TYPE_PURCHASE:
                    $sourceType = 'Purchased';
                    break;
                case CA_SOURCE_TYPE_REWARD:
                    $sourceType = 'Reward';
                    break;
                case CA_SOURCE_TYPE_DEDUCTION:
                    $sourceType = 'Deducted';
                    break;
                case CA_SOURCE_TYPE_NONE:
                default:
                    $sourceType = '';
                    break;
            }
            
            //check the coins type
            switch ($aRow['coinsType'])
            {
                case CA_COIN_TYPE_SILVER:
                    $coinType = 'Silver coins';
                    break;
                case CA_COIN_TYPE_GOLD:
                    $coinType = 'Gold coins';
                    break;
                default:
                    $coinType = 'Unknown coins';
                    break;
            }
            
            //check the exchange type
            switch ($aRow['exchangeType'])
            {
                case CA_EXCHANGE_TYPE_MINUS:
                    $exchangeType = '-';
                    break;
                case CA_EXCHANGE_TYPE_PLUS:
                default:
                    $exchangeType = '';
                    break;
            }
                            
            //Set the first two columns
            $row = array();
            $row[0] = $aRow['id'];
            $row[1] = $sourceType;
            $row[2] = $exchangeType.'<b>'.$aRow['amount'].' '.$coinType.'</b>';
            $row[3] = $aRow['source'];
            $row[4] = $aRow['time'];
            
            return $row;
        });
    }

    public function store_logs()
    {
        $this->get_pstore_logs('STORE');
    }

    public function armorsets_logs()
    {
        $this->get_pstore_logs('PSTORE_ARMORSETS');
    }

    public function levels_logs()
    {
        $this->get_pstore_logs('PSTORE_LEVEL');
    }

    public function racechange_logs()
    {
        $this->get_pstore_logs('PSTORE_RACE');
    }

    public function factionchange_logs()
    {
        $this->get_pstore_logs('PSTORE_FACTION');
    }

    public function customization_logs()
    {
        $this->get_pstore_logs('PSTORE_CUSTOMIZE');
    }

    public function gamegold_logs()
    {
        $this->get_pstore_logs('PSTORE_GOLD');
    }

    public function boosts_logs()
    {
        $this->get_pstore_logs('BOOSTS');
    }

    private function get_pstore_logs($sSource)
    {
        $this->CheckPermissionSilent(PERMISSION_LOGS);

        $aColumns = array('id', 'text', 'account', 'time', 'status', 'data', 'ip_address');
        $sIndexColumn = "id";
        $sTable = 'purchase_log';
        $sAddWhere = "`source` = ".$this->db->quote($sSource);

        $this->get_data($aColumns, $sIndexColumn, $sTable, $sAddWhere, function ($aRow)
        {
            //split the text
            $textArr = explode('| Update:', $aRow['text']);
            $text = '';
            $firstLine = $textArr[0];
            foreach ($textArr as $i => $val)
            {
                if ($i == 0) continue;
                $text .= $val . '<br />';
            }

            if ($aRow['data'])
            {
                $text .= '<br/>';
                $data = json_decode($aRow['data'], true);
                foreach ($data as $key => $value)
                {
                    $text .= ''.$key.': <strong>'.$value.'</strong>, ';
                }
                $text = substr($text, 0, strlen($text) - 2);
                unset($data);
            }
            
            //find the account
            $res = $this->db->prepare("SELECT displayName FROM `account_data` WHERE `id` = :id LIMIT 1;");
            $res->bindParam(':id', $aRow['account'], PDO::PARAM_INT);
            $res->execute();
            //if we found it
            if ($res->rowCount() > 0)
            {
                $row = $res->fetch();
                $aRow['account'] = '<a href="'.base_url().'/admin/users/view?uid='.$aRow['account'].'">' . $row['displayName'] . '</a> [' . $aRow['account'] . ']';
                unset($row);
            }
            unset($res);

            //Set the first two columns
            $row = array();
            $row[0] = $aRow['id'];
            $row[1] = '
                <div class="datatable-expander" style="position: relative;">
                    <p>'.$firstLine.'</p>
                    <span style="position: absolute; top: 1px; right: 0px;">
                        <a href="#" onclick="return ToggleExpand(this);">Open</a>
                    </span>
                    <p id="content" style="display: none; width: 80%">
                        '.$text.'
                    </p>
                </div>';
            $row[2] = $aRow['account'];
            $row[3] = $aRow['time'];
            $row[4] = $aRow['ip_address'];
            $row[5] = ucfirst($aRow['status']);
            
            return $row;
        });
    }

    private function get_data($aColumns, $sIndexColumn, $sTable, $sAddWhere = false, $rowCallback = false)
    {
        $sLimit = "";
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
        {
            $sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
                intval( $_GET['iDisplayLength'] );
        }
        
        $sOrder = "";
        if ( isset( $_GET['iSortCol_0'] ) )
        {
            $sOrder = "ORDER BY  ";
            for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
            {
                if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
                {
                    $sOrder .= "`".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."` ".
                        ($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
                }
            }
            
            $sOrder = substr_replace( $sOrder, "", -2 );
            if ( $sOrder == "ORDER BY" )
            {
                $sOrder = "";
            }
        }
        
        $sWhere = "";
        if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
        {
            $sWhere = "WHERE (";
            for ( $i=0 ; $i<count($aColumns) ; $i++ )
            {
                $sWhere .= "`".$aColumns[$i]."` LIKE ".$this->db->quote('%'.$_GET['sSearch'].'%')." OR ";
            }
            $sWhere = substr_replace( $sWhere, "", -3 );
            $sWhere .= ')';
        }
        
        /* Individual column filtering */
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
            {
                if ( $sWhere == "" )
                {
                    $sWhere = "WHERE ";
                }
                else
                {
                    $sWhere .= " AND ";
                }
                $sWhere .= "`".$aColumns[$i]."` LIKE ".$this->db->quote('%'.$_GET['sSearch_'.$i].'%')." ";
            }
        }

        if ( $sAddWhere !== false )
        {
            if ( $sWhere == "" )
            {
                $sWhere = "WHERE ";
            }
            else
            {
                $sWhere .= " AND ";
            }
            $sWhere .= $sAddWhere." ";
        }
        
        $sQuery = "
            SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $aColumns))."`
            FROM   $sTable
            $sWhere
            $sOrder
            $sLimit";
        $rResult = $this->db->query( $sQuery);
        
        /* Data set length after filtering */
        $sQuery = "SELECT FOUND_ROWS()";
        $rResultFilterTotal = $this->db->query( $sQuery);
        $aResultFilterTotal = $rResultFilterTotal->fetch(PDO::FETCH_NUM);
        $iFilteredTotal = $aResultFilterTotal[0];
        
        /* Total data set length */
        $sQuery = "SELECT COUNT(`".$sIndexColumn."`) FROM $sTable";
        $rResultTotal = $this->db->query( $sQuery);
        $aResultTotal = $rResultTotal->fetch(PDO::FETCH_NUM);
        $iTotal = $aResultTotal[0];
        
        $output = array(
            "sEcho" => isset($_GET['sEcho']) ? intval($_GET['sEcho']) : 0,
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
        
        while ( $aRow = $rResult->fetch() )
        {
            $row = array();

            if ($rowCallback !== false && is_callable($rowCallback))
            {
                $row = call_user_func($rowCallback, $aRow);
            }
            
            //Now we have to pull 
            $output['aaData'][] = $row;
        }
        
        echo json_encode( $output );
        exit;
    }
}