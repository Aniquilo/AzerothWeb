<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class PollsLib
{
    static public function GetPoll()
    {
        $CORE =& get_instance();

        //Get the latest poll
        $res = $CORE->db->prepare("SELECT * FROM `polls` WHERE `disabled` = '0' ORDER BY `id` DESC LIMIT 1;");
        $res->execute();

        if ($res->rowCount() > 0)
        {
            $poll = $res->fetch();
            $poll['answers'] = PollsLib::GetAnswers($poll['id']);

            return $poll;
        }

        return false;
    }

    static public function GetAnswers($pollId)
    {
        $CORE =& get_instance();

        $totalVotes = 0;

        $sth = $CORE->db->prepare("SELECT * FROM `polls_answers` WHERE `poll_id` = ? ORDER BY `id` ASC;");
        $sth->execute(array($pollId));

        if ($sth->rowCount() > 0)
        {
            $answers = $sth->fetchAll();

            foreach ($answers as $i => $v)
            {
                $sth2 = $CORE->db->prepare("SELECT COUNT(*) AS `count` FROM `polls_votes` WHERE `poll_id` = ? AND `answer_id` = ?;");
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

    static public function HasAnswer($pollId)
    {
        $CORE =& get_instance();

        $res = $CORE->db->prepare("SELECT * FROM `polls_votes` WHERE `poll_id` = ? AND `user_id` = ?;");
        $res->execute(array($pollId, $CORE->user->get('id')));

        if ($res->rowCount() > 0)
        {
            return true;
        }

        return false;
    }
}