<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Role
{
    protected $id;
    protected $permissions;

    protected function __construct()
    {
        $this->permissions = array();
    }

    // return a role object with associated permissions
    public static function getRolePerms($role_id)
    {
        $CORE =& get_instance();

        $role = new Role();
        $role->id = $role_id;

        $sql = "SELECT t2.perm_id FROM `rbac_role_perm` as t1
                JOIN `rbac_permissions` as t2 ON t1.perm_id = t2.perm_id
                WHERE t1.role_id = :role_id";
        $sth = $CORE->db->prepare($sql);
        $sth->execute(array(":role_id" => $role_id));

        while ($row = $sth->fetch(PDO::FETCH_ASSOC))
        {
            $role->permissions[$row["perm_id"]] = true;
        }

        return $role;
    }

    public function getId()
    {
        return $this->id;
    }
    
    // check if a permission is set
    public function hasPerm($permission)
    {
        return isset($this->permissions[$permission]);
    }
}