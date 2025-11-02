<?php namespace App\Models;

use CodeIgniter\Model;

class PaymentLinkModel extends Model
{
    protected $table = 'payment_links';
    protected $primaryKey = 'id';
    protected $allowedFields = ['loan_id','token','amount','expires_at','used'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}
