<?php

use Stripe\StripeClient;

class CreditsController extends BaseController
{
    protected $secret_key = null;

    /**
     * @throws \Stripe\Exception\ApiErrorException
     */

    public function getStripeKey()
    {
        $query = "SELECT * FROM settings WHERE name = 'stripe'";

        $stripe = $this->conn->GetDataFromDb($query);

        $this->secret_key = $stripe['production_key'];

        if ($stripe['dev_active']) {
            $this->secret_key = $stripe['development_key'];
        }

    }

    public function purchaseCredits($request)
    {
        
        if (!isset($request['creditsAmount']) || 
            !isset($request['cardNumber']) || 
            !isset($request['expiryMonth']) || 
            !isset($request['expiryYear']) || 
            !isset($request['cvc']) ||
            !isset($request['name']) ||
            !isset($request['address']) ) {
            return $this->response(false, "Please fill up the missing fields!", $request);
        }

        if ($request['creditsAmount'] < 10) {
            return $this->response(false, "Minimum credits amount is 10");
        }

        if (!$request['email']) {
            return $this->response(false, "Email is missing.");
        }

        $cardNumber = $request['cardNumber'];
        $exp_month = $request['expiryMonth'];
        $exp_year = $request['expiryYear'];
        $cvc = $request['cvc'];
        $name = $request['name'];
        $address = $request['address'];

        $email = $this->conn->escape($request['email']);
        $userID = $this->conn->escape($request['user_id']);
        $creditsAmount = $this->conn->escape($request['creditsAmount']);

        $this->getStripeKey();

        try {

            $stripe = new \Stripe\StripeClient($this->secret_key);

            $token = $stripe->tokens->create([
              'card' => [
                'number'        => $cardNumber,
                'exp_month'     => $exp_month,
                'exp_year'      => $exp_year,
                'cvc'           => $cvc,
                'name'          => $name,
                'address_line1' => $address
              ]
            ]);

            if ($token->id) {

                $metadata = [
                    'userId'  => $userID,
                    'email'   => $email
                ];

                $charge = $stripe->charges->create([
                    'receipt_email' => $email,
                    'amount'        => $creditsAmount * 200,
                    'currency'      => 'usd',
                    'source'        => $token,
                    'description'   => $creditsAmount . ' ' . 'credits',
                    'metadata'      => $metadata
                ]);

                $response = json_encode($charge);

                if ($charge->status === 'succeeded') {

                    $addToPurchaseHistoryQuery = "
                    INSERT INTO purchase_history (userID, creditsAmount, status, receiptUrl, stripeResponse) 
                    VALUES 
                    ('$userID', '$creditsAmount', 'success','$charge->receipt_url', '$response')";

                    if (!$this->conn->ExecuteQuery($addToPurchaseHistoryQuery)) {
                        return $this->response(false, "An error occurred, please try again later!");
                    }

                    $u = new UserController($this->conn);
                    $coupon = $u->checkCouponAvailability($request['user_id']);
                    $referrer_id = null;
                    $referrer_msg = null;

                    if ($coupon) {
                        $this->conn->ExecuteQuery("UPDATE user_coupon_referral SET isUsed = 1 WHERE referral_id = '$userID'");

                        $creditsAmount += 2;

                        $referrer_id = $coupon['userId'];
                        $update_referral = $this->conn->ExecuteQuery("UPDATE users SET currentCredits = currentCredits + 2 WHERE id = '$referrer_id'");

                        $referrer_msg = "You have got 2 credits for referring " . $coupon['name'];
                    }

                    $updateCurrentCreditsQuery = "UPDATE users SET currentCredits = currentCredits + '$creditsAmount' WHERE id = '$userID'";

                    $referral_message = "You have got 2 credits for using coupon on sign up page.";

                    $this->conn->ExecuteQuery("
                        INSERT INTO purchase_history (userID, status, creditsAmount, message) 
                        VALUES ('$userID', 'success','2', '$referral_message'),
                        ('$referrer_id', 'success','2', '$referrer_msg')");

                    if (!$this->conn->ExecuteQuery($updateCurrentCreditsQuery)) {
                        return $this->response(false, "An error occurred, please try again later!");
                    }

                    return $this->response(true, "Credits purchased successfully, check your receipt url in 'Purchase history' tab.");

                }else{
                    $addToPurchaseHistoryQuery = "
                    INSERT INTO purchase_history (userID, creditsAmount, status, receiptUrl, stripeResponse) 
                    VALUES 
                    ('$userID', '$creditsAmount', 'failed','', '$response')";
                    if (!$this->conn->ExecuteQuery($addToPurchaseHistoryQuery)) {
                        return $this->response(false, "An error occurred when charging bill. Please try again later!");
                    }
                }
            }        

            return $this->response(false, "Failed to generate token!", $token);

        } catch (Exception $e) {
            return $this->response(false, "An error occured!", $e);
        }

    
    }

    public function getCreditsHistory($request)
    {
        $data = [];
        $id = $this->conn->escape($request['id']);

        $query = "SELECT creditsAmount, item, actionDate FROM credits_history WHERE userID = '$id' ORDER BY id DESC LIMIT 20";
        $data = $this->conn->GetRows($query);

        foreach ($data as $key => $value) {
            $data[$key]['actionDate'] = date('m/d/Y', strtotime($value['actionDate']));
            $data[$key]['date_beautified'] = date('F d, Y H:i:s', strtotime($value['actionDate']));
        }

        return $this->response(true, "", $data);

    }

    public function getPurchaseHistory($request)
    {
        $data = [];
        $id = $this->conn->escape($request['id']);

        $query = "SELECT creditsAmount, receiptUrl, purchaseDate, status, stripeResponse, message FROM purchase_history WHERE userID = '$id' ORDER BY id DESC LIMIT 20";
        $data = $this->conn->GetRows($query);

        foreach ($data as $key => $value) {
            $data[$key]['purchaseDate'] = date('m/d/Y', strtotime($value['purchaseDate']));
        }

        return $this->response(true, "", $data);

    }

    public function getCurrentCredits($request)
    {
        $data = [];
        $id = $this->conn->escape($request['id']);

        $query = "SELECT currentCredits FROM users WHERE id = '$id'";
        $data = $this->conn->GetRows($query);

        return $this->response(true, "", $data);
    }


}
