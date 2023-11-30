<?php

class M_institution extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function retrieveItems()
    {
        $this->db->where('company_id', $_SESSION['company_id']);
        $query = $this->db->get('pos_plaid_items');

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return '';
        }
    }

    function retrieveAccountsByItemID($item_id)
    {
        $this->db->where(array('item_id' => $item_id, 'company_id' => $_SESSION['company_id']));
        $query = $this->db->get('pos_plaid_accounts');

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return '';
        }
    }

    function retrieveTransactionsByAccountID($account_id)
    {
        $this->db->where(array('account_id' => $account_id, 'company_id' => $_SESSION['company_id']));
        $query = $this->db->get('pos_plaid_transactions');

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return '';
        }
    }

    function is_transaction_exist($trans_id)
    {
        $this->db->where(array('plaid_trans_id' => $trans_id, 'company_id' => $_SESSION['company_id']));
        $query = $this->db->get('acc_entry_items');

        if ($query->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    function is_ItemByPlaidInstitutionId($institutionId)
    {
        $this->db->where(array('plaid_institution_id' => $institutionId, 'company_id' => $_SESSION['company_id']));
        $query = $this->db->get('pos_plaid_items');

        if ($query->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    function is_account_exist($account_id)
    {
        $this->db->where(array('plaid_account_id' => $account_id, 'company_id' => $_SESSION['company_id']));
        $query = $this->db->get('pos_plaid_accounts');

        if ($query->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    function is_plaid_transaction_exist($transaction_id)
    {
        $this->db->where(array('plaid_transaction_id' => $transaction_id, 'company_id' => $_SESSION['company_id']));
        $query = $this->db->get('pos_plaid_transactions');

        if ($query->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function insert_plaid_items($data)
    {
        $this->db->insert('pos_plaid_items', $data);
        return $this->db->insert_id();
    }

    public function insert_plaid_accounts($data)
    {
        $this->db->insert('pos_plaid_accounts', $data);
        return $this->db->insert_id();
    }

    public function insert_plaid_transactions($data)
    {
        $this->db->insert('pos_plaid_transactions', $data);
        return $this->db->insert_id();
    }

    public function delete_plaid_items($item_id)
    {
        $this->db->trans_start();

        //retrive plaid account and then delete transactions aginst account id
        $plaid_accounts = $this->retrieveAccountsByItemID($item_id);
        foreach ($plaid_accounts as $values) {
            $this->db->delete('pos_plaid_transactions', array('account_id' => $values['plaid_account_id']));
        }
        $this->db->delete('pos_plaid_accounts', array('item_id' => $item_id));
        $this->db->delete('pos_plaid_items', array('plaid_item_id' => $item_id));

        $this->db->trans_complete();
    }
}
