<?php namespace App\Controllers;

use App\Models\LoanModel;
use App\Models\PaymentLinkModel;
use CodeIgniter\Controller;

class Loans extends Controller
{
    public function index()
    {
        $loanModel = new LoanModel();
        $data['loans'] = $loanModel->findAll();
        return view('loans/index', $data);
    }

    public function generateLink()
    {
        helper('text'); // optionally for random_string

        $request = service('request');
        if (!$request->isAJAX() || !$request->getMethod() === 'post') {
            return $this->response->setStatusCode(405);
        }

        $loan_id = $request->getPost('loan_id');
        $type    = $request->getPost('type'); // 'full' or 'custom'
        $custom_amount = $request->getPost('custom_amount');

        // Basic validation
        if (empty($loan_id) || !in_array($type, ['full','custom'])) {
            return $this->response->setJSON(['status'=>'error','message'=>'Invalid data'])->setStatusCode(400);
        }

        $loanModel = new LoanModel();
        $loan = $loanModel->find($loan_id);
        if (!$loan) {
            return $this->response->setJSON(['status'=>'error','message'=>'Loan not found'])->setStatusCode(404);
        }

        if ($type === 'full') {
            $amount = $loan['amount_due'];
        } else {
            $amount = (float)$custom_amount;
            if ($amount <= 0 || $amount > $loan['amount_due']) {
                return $this->response->setJSON(['status'=>'error','message'=>'Invalid custom amount'])->setStatusCode(400);
            }
        }

        // generate secure unique token
        $token = bin2hex(random_bytes(16)); // 32 char hex

        // save to payment_links
        $plModel = new PaymentLinkModel();
        $data = [
            'loan_id' => $loan_id,
            'token' => $token,
            'amount' => $amount,
            // optionally add expires_at here
        ];
        $plModel->insert($data);

        // create the link (for testing we point to a local route; change base URL as needed)
        $link = base_url("pay/$token"); // e.g. http://localhost:8080/pay/<token>

        return $this->response->setJSON(['status'=>'ok','link'=>$link,'token'=>$token,'amount'=>$amount]);
    }

    // Optional: endpoint to show the link (simulate payment page)
    public function pay($token = null)
    {
        $plModel = new PaymentLinkModel();
        $link = $plModel->where('token', $token)->first();
        if (!$link) {
            return view('loans/payment_notfound');
        }
        $data['link'] = $link;
        return view('loans/payment_page', $data);
    }
}
