<?php 
if (!defined('init_engine'))
{
    header('HTTP/1.0 404 not found');
    exit;
}

require_once ROOTPATH . '/engine/admin_controller.php';

class Polls extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->CheckPermission(PERMISSION_MAN_POLLS);
    }

    public function index()
    {
        $hasCurrent = false;

        $polls = false;
        $sth = $this->db->prepare("SELECT * FROM `polls` ORDER BY `id` DESC;");
        $sth->execute();

        if ($sth->rowCount() > 0)
        {
            $polls = $sth->fetchAll();

            foreach ($polls as $i => $poll)
            {
                $polls[$i]['current'] = false;

                if ((int)$poll['disabled'] == 0 && !$hasCurrent)
                {
                    $polls[$i]['current'] = true;
                    $hasCurrent = true;
                }

                $polls[$i]['answers'] = $this->getAnswers($poll['id']);                
            }
        }

        //Print the page
        $this->PrintPage('polls/polls', array('polls' => $polls));
    }

    private function getAnswers($pollId)
    {
        $totalVotes = 0;

        $sth = $this->db->prepare("SELECT * FROM `polls_answers` WHERE `poll_id` = ? ORDER BY `id` ASC;");
        $sth->execute(array($pollId));

        if ($sth->rowCount() > 0)
        {
            $answers = $sth->fetchAll();

            foreach ($answers as $i => $v)
            {
                $sth2 = $this->db->prepare("SELECT COUNT(*) AS `count` FROM `polls_votes` WHERE `poll_id` = ? AND `answer_id` = ?;");
                $sth2->execute(array($pollId, $v['id']));
                $row = $sth2->fetch();

                $answers[$i]['votes'] = (int)$row['count'];
                $totalVotes += (int)$row['count'];
            }

            foreach ($answers as $i => $v)
            {
                if ($totalVotes > 0)
                {
                    $answers[$i]['pct'] = round(((int)$v['votes'] / $totalVotes) * 100, 1);
                }
                else
                {
                    $answers[$i]['pct'] = 0;
                }
            }

            return $answers;
        }

        return false;
    }

    public function submit()
    {
        //prepare multi errors
        $this->errors->NewInstance('polls');

        $question = isset($_POST['question']) ? $_POST['question'] : false;
        $answers = isset($_POST['answers']) ? $_POST['answers'] : false;

        if (!$question)
        {
            $this->errors->Add('Please enter poll question.');
        }

        if (!$answers || !is_array($answers) || count($answers) < 2)
        {
            $this->errors->Add('Please enter at least 2 poll answers.');
        }

        $this->errors->Check('/admin/polls');

        $sth = $this->db->prepare("INSERT INTO `polls` (`question`) VALUES (?);");
        $sth->execute(array($question));

        if ($sth->rowCount() > 0)
        {
            $poll_id = $this->db->lastInsertId();

            foreach ($answers as $i => $answer)
            {
                $sth = $this->db->prepare("INSERT INTO `polls_answers` (`poll_id`, `answer`) VALUES (?, ?);");
                $sth->execute(array($poll_id, $answer));
            }

            //bind on success
            $this->errors->onSuccess('The poll was successfully created.', '/admin/polls');
            $this->errors->triggerSuccess();
        }
        else
        {
            $this->errors->Add('Failed to create the poll record.');
        }

        $this->errors->Check('/admin/polls');
        exit;
    }

    public function delete()
    {
        $id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

        //prepare multi errors
        $this->errors->NewInstance('polls');
        
        //bind on success
        $this->errors->onSuccess('The poll was successfully deleted.', '/admin/polls');
        
        if (!$id)
        {
            $this->errors->Add("The poll id is missing.");
        }

        $this->errors->Check('/admin/polls');
        
        $delete = $this->db->prepare('DELETE FROM `polls` WHERE `id` = :id LIMIT 1;');
        $delete->bindParam(':id', $id, PDO::PARAM_INT);
        $delete->execute();
        
        if ($delete->rowCount() < 1)
        {
            $this->errors->Add("Failed to delete the poll.");
        }
        else
        {
            $delete = $this->db->prepare('DELETE FROM `polls_answers` WHERE `poll_id` = :id;');
            $delete->bindParam(':id', $id, PDO::PARAM_INT);
            $delete->execute();

            $delete = $this->db->prepare('DELETE FROM `polls_votes` WHERE `poll_id` = :id;');
            $delete->bindParam(':id', $id, PDO::PARAM_INT);
            $delete->execute();

            $this->errors->triggerSuccess();
        }
            
        $this->errors->Check('/admin/polls');
        exit;
    }

    public function disable()
    {
        $id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

        //prepare multi errors
        $this->errors->NewInstance('polls');
        
        //bind on success
        $this->errors->onSuccess('The poll was successfully disabled.', '/admin/polls');
        
        if (!$id)
        {
            $this->errors->Add("The poll id is missing.");
        }

        $this->errors->Check('/admin/polls');
        
        $delete = $this->db->prepare("UPDATE `polls` SET `disabled` = '1' WHERE `id` = :id LIMIT 1;");
        $delete->bindParam(':id', $id, PDO::PARAM_INT);
        $delete->execute();
        
        if ($delete->rowCount() < 1)
        {
            $this->errors->Add("Failed to disable the poll.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
            
        $this->errors->Check('/admin/polls');
        exit;
    }

    public function enable()
    {
        $id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

        //prepare multi errors
        $this->errors->NewInstance('polls');
        
        //bind on success
        $this->errors->onSuccess('The poll was successfully enabled.', '/admin/polls');
        
        if (!$id)
        {
            $this->errors->Add("The poll id is missing.");
        }

        $this->errors->Check('/admin/polls');
        
        $delete = $this->db->prepare("UPDATE `polls` SET `disabled` = '0' WHERE `id` = :id LIMIT 1;");
        $delete->bindParam(':id', $id, PDO::PARAM_INT);
        $delete->execute();
        
        if ($delete->rowCount() < 1)
        {
            $this->errors->Add("Failed to enable the poll.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
            
        $this->errors->Check('/admin/polls');
        exit;
    }
}