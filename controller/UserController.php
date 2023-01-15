<?php
/**
 * 
 */
class UserController extends BaseController
{

    public function getUsers($request = []) {
        
        $data = [];

        $query = "SELECT name, email, expireOn, id, user_type, currentCredits, couponCode FROM users ORDER BY id DESC";

        $data = $this->conn->GetRows($query);

        foreach ($data as $key => $value) {
            $data[$key]['expiry_beautified'] = date('m/d/Y', strtotime($value['expireOn']));

            if (new DateTime() > new DateTime($value['expireOn']))
                $data[$key]["isExpired"] = true;
            else
                $data[$key]["isExpired"] = false;
        }

        return $this->response(true, "", $data);

    }

    public function findUser($request) {

        $data = [];

        $query = "SELECT * FROM users WHERE id = '" . $this->conn->escape($request['id']) .  "'";

        $data = $this->conn->GetDataFromDb($query);

        $coupon = $this->checkCouponAvailability($request['id']);

        if ($coupon) {
            $data['hasCoupon'] = true;
        }

        if ($data) {
            unset($data['password']);
            return $this->response(true, "Data found", $data);
        }

        return $this->response(false, "No user found");
    }

    public function createUser($request) {

        if (isset($request['expireOn'])) {
            $request['expireOn'] = date('Y-m-d H:i:s',  strtotime($request['expireOn']));
        }

        if (!isset($request['name'])) {
            return $this->response(false, "Name is required.");
        }

        if (!isset($request['email'])) {
            return $this->response(false, "Email is required.");
        }

        if (!filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {          
          return $this->response(false, "Invalid email format.");
        }

        if (!isset($request['password']) || !isset($request['confirmPassword'])) {
            return $this->response(false, "Passwords are required.");
        }

        if (strlen($request['password']) <= 4) {
            return $this->response(false, "Password must have a minimum of 5 characters.");
        }

        if ($request['password'] != $request['confirmPassword']) {
            return $this->response(false, "Passwords don't match.");
        }

        $coupon = null;

        if (isset($request['couponCode']) && $request['couponCode'] != '') {

            $couponCode = $request['couponCode'];

            $coupon = $this->conn->GetDataFromDb("SELECT id, couponCode 
            FROM users WHERE couponCode = '$couponCode' ");

            if (!$coupon) {
                return $this->response(false, "Invalid coupon code.");
            }

        }

        $password = password_hash($request['confirmPassword'], PASSWORD_BCRYPT);

        $name = $this->conn->escape($request['name']);
        $email = $this->conn->escape($request['email']);
        $password = $this->conn->escape($password);

        if ($this->conn->NumOfRows("SELECT id FROM users WHERE LOWER(email) = '$email'") == 0) {        

            $token = $this->conn->generateRandomString(20);  

            $query = "INSERT INTO users (name, email, password, expireOn, token) VALUES ('$name','$email','$password', NOW(), '$token')";

            if ($this->conn->ExecuteQuery($query)) {                

                $data = $this->conn->GetDataFromDb("SELECT id, password, picture, name, user_type, couponCode FROM users WHERE email='$email'");

                $couponCode = 'AID-'.$data['id'].strtoupper(substr($data['name'], 0, 1));

                $data['couponCode'] = $couponCode;

                $this->conn->ExecuteQuery("UPDATE users SET couponCode = '$couponCode' WHERE id = '" . $data['id'] . "'");

                if (isset($coupon['id'])) {
                    $this->conn->ExecuteQuery("
                        INSERT INTO user_coupon_referral (userId, referral_id, couponCode, isUsed)
                        VALUES
                        ('" . $coupon['id'] . "','" . $data['id'] . "', '$couponCode', '0')");
                }
       
                $data = [
                    'token' => $token,
                    'userData'  => $data
                ];

                return $this->response(true, "User successfully created.", $data);
            }

        }else{
            return $this->response(false, "Email is already in use.");
        }

        return $this->response(false, "Something went wrong.");
    }

    public function updateUser($request) {

        //disable account
        if (isset($request['expireOn']) && !isset($request['user_type']))
            $request['expireOn'] = 'NOW()';
        else if (isset($request['expireOn'])) //edit account
            $request['expireOn'] = date('Y-m-d H:i:s', strtotime($request['expireOn']));

        if (isset($request['email'])) {
            $check_email = $this->conn->GetDataFromDb("SELECT email FROM users WHERE email = '" . $this->conn->escape($request['email']) . "' AND id <> '" . $this->conn->escape($request['id']) . "' ");

            if (count($check_email)) {
                return $this->response(false, "Email is already in use");
            }
        }

        if (isset($request['new_password']) && isset($request['changePasswordByAdmin'])) {
            $request['password'] = password_hash($request['new_password'], PASSWORD_BCRYPT);
        }

        //password change from members area
        if (isset($request['current_password']) && isset($request['new_password']) && isset($request['confirm_password'])) {

            if ($request['new_password'] != $request['confirm_password']) {
                return $this->response(false, "New password and confirm password don't match");
            }

            $password_data = $this->conn->GetDataFromDb("SELECT password FROM users WHERE id = '" . $this->conn->escape($request['id']) . "'");

            if (!password_verify($request['current_password'], $password_data['password'])) {
                return $this->response(false, "Incorrect old password");
            }

            $request['password'] = password_hash($request['new_password'], PASSWORD_BCRYPT);

        }

        unset($request['user_id']);
        unset($request['current_password']);
        unset($request['new_password']);
        unset($request['confirm_password']);
        unset($request['expiry_beautified']);
        unset($request['isExpired']);
        unset($request['changePasswordByAdmin']);

        if (isset($request['name'])) {
            $_SESSION['memberName'] = $request['name'];
        }

        return parent::update($request);
    }

    public function signupOrUpdate($request, $sendgrid) {

        $expire = "INTERVAL 1 MONTH";

        if ($request['interval'] == 'year')
            $expire = "INTERVAL 1 YEAR";

        $password = $this->randomPassword();
        $ePassword = password_hash($password, PASSWORD_BCRYPT);

        $email = $this->conn->escape($request['email']);
        $accType = $this->conn->escape($request['accType']);
        $planID = $this->conn->escape($request['planID']);
        $cardID = $this->conn->escape($request['cardID']);
        $name = $this->conn->escape($request['name']);
        $customerID = $this->conn->escape($request['customerID']);
        $firstName = $name;
        if (strpos($name, " ") !== false) {
            $tName = explode(' ', $name);
            $firstName = $tName[0];
        }

        if ($this->conn->NumOfRows("SELECT id FROM users WHERE LOWER(email) = '$email'") == 0) {

            $query = "INSERT INTO users(cardID,customerID,name,email,password,lastUpdated,createdOn,expireOn,planID,accType)
                    VALUES ('$cardID','$customerID','$name','$email', '$ePassword', NOW(), NOW(), DATE_ADD(NOW(), $expire), '$planID', '$accType')";

            $this->conn->ExecuteQuery($query);

            $this->sendWelcomeEmail($email, $firstName, $password, $sendgrid);
        } else {
            $query = "UPDATE users SET cardID='$cardID',customerID='$customerID',expireOn = DATE_ADD(NOW(), $expire), accType='$accType', planID='$planID' WHERE LOWER(email)='$email'";
            $this->conn->ExecuteQuery($query);
            $this->sendReBillEmail($firstName,$email,$sendgrid);
        }

        return $this->response(true, "User saved!");
    }

    private function sendWelcomeEmail($email, $firstName, $pass, $sendgrid) {

        if (!$firstName) {
            $firstName = "Hello";
        }

        $settings = new SettingController($this->conn);
        $mail = $settings->getMailSettings();

        $subject = $mail['data']['new_purchase']['subject'];

        $body = str_replace(['::FIRST_NAME::', '::EMAIL::', '::PASSWORD::'], [$firstName, $email, $pass], $mail['data']['new_purchase']['body']);

        $sendgrid->sendAnEmail($email, $subject, $body);
    }

    private function sendReBillEmail($firstName, $email, $sendgrid) {

        if (!$firstName) {
            $firstName = "Hello";
        }

        $settings = new SettingController($this->conn);
        $mail = $settings->getMailSettings();
        $pass = null;

        $subject = $mail['data']['rebill']['subject'];
        $body = str_replace(['::FIRST_NAME::', '::EMAIL::', '::PASSWORD::'], [$firstName, $email, $pass], $mail['data']['rebill']['body']);

        $sendgrid->sendAnEmail($email, $subject, $body);
    }

    public function authenticateUserRequest($request) {

        $query = "SELECT id, token FROM users WHERE id = '" . $this->conn->escape($request['user_id']) . "' AND token = '" . $this->conn->escape($request['_token']) . "' ";

        $data = $this->conn->GetDataFromDb($query);

        if (!empty($data)) {
            return $this->response(true, 'User verified');
        }

        return $this->response(false, 'Error 401 : Unauthorized Access');
    }

    public function checkCouponAvailability($userId) {

        $id = $this->conn->escape($userId);

        $query = "SELECT ucr.*, u.name FROM user_coupon_referral ucr LEFT JOIN users u ON u.id = ucr.referral_id WHERE ucr.referral_id = '$id' AND ucr.isUsed = 0";

        $data = $this->conn->GetDataFromDb($query);

        return $data;

    }


}
