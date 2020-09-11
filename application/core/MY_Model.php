<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    protected $table = null;

    public function __construct(string $table)
    {
        parent::__construct();

        $this->table = $table;
    }

    public function getEntries($filters = null, $orderBy = null)
    {
        $this->parseFilters($filters);
        $this->parseOrderBy($orderBy);

        $res = $this->db->get($this->table);

        if (!$res || $res->num_rows() == 0)
            return array();

        return $res->result();
    }

    public function getEntry($filters, $orderBy = null)
    {
        $this->parseFilters($filters);
        $this->parseOrderBy($orderBy);

        $res = $this->db->get($this->table);

        if (!$res || $res->num_rows() == 0)
            return false;

        return $res->row();
    }

    public function editEntry($values, $filters = null)
    {
        $this->db->set($values);

        $this->parseFilters($filters);

        $this->db->update($this->table);

        return $this->db->affected_rows();
    }

    public function saveEntry($values)
    {
        $this->db->set($values);

        $this->db->insert($this->table);

        return $this->db->affected_rows();
    }

    public function deleteEntry($filters)
    {
        if (is_null($filters))
            return 0;

        $this->parseFilters($filters);

        $this->db->delete($this->table);

        return $this->db->affected_rows();
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    private function parseFilters($filters)
    {
        if (!is_null($filters)) {
            if (is_string($filters))
                $this->db->where($filters);

            if (is_array(($filters))) {
                foreach ($filters as $k => $v)
                    $this->db->where($k, $v);
            }
        }
    }

    private function parseOrderBy($orderBy)
    {
        if (!is_null($orderBy)) {
            if (is_string($orderBy))
                $this->db->order_by($orderBy);

            if (is_array(($orderBy))) {
                foreach ($orderBy as $k => $v)
                    $this->db->order_by($k, $v);
            }
        }
    }
}
