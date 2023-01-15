<?php
include('app.php');

if (isset($_GET['action']) && isset($_GET['entity'])) {
	
	$action = $_GET['action'];
	$entity = $_GET['entity'];

	$data = [];

	$request_body = file_get_contents('php://input');

	$data = (array)json_decode($request_body);

	if (!$data) {
		$data = $_REQUEST;
	}

	$action_exceptions = ['login', 'login_member', 'reset_password', 'create_user'];

	if (!in_array($action, $action_exceptions) && !isset($_GET['is_admin']) && isset($data['_token'])) {

		$verify = new UserController($DB);
		
		$response = $verify->authenticateUserRequest($data);

		if (!$response['success']) {
			exit(json_encode($response));
		}else{
			unset($data['_token']);
		}
	}

	switch ($entity) {

		case 'admin':

			$a = new AdminController($DB);

			switch ($action) {
				case 'get_admin':
                    exit(json_encode($a->find($data)));
                    break;
                case 'update_admin':
                    exit(json_encode($a->updateAdmin($data)));
                    break;
				default:
					exit(json_encode(['data'=> $data, 'success'=>false, 'error'=> 'Action not found']));
					break;
			}

			break;

		case 'user':

			$u = new UserController($DB);

			switch ($action) {
				case 'get_user':
                    exit(json_encode($u->findUser($data)));
                    break;
                case 'get_users':
                    exit(json_encode($u->getUsers($data)));
                    break;
                case 'create_user':
                    exit(json_encode($u->createUser($data)));
                    break;
				case 'update_user':
					exit(json_encode($u->updateUser($data)));
					break;
				case 'signupOrUpdate':
					$sendgrid = new SendGridService($DB);
					exit(json_encode($u->signupOrUpdate($data, $sendgrid)));
					break;
				default:
					exit(json_encode(['data'=> $data, 'success'=>false, 'error'=> 'Action not found']));
					break;
			}

			break;

		case 'setting':

			$s = new SettingController($DB);

			switch ($action) {
                case 'save_settings':
                	exit(json_encode($s->saveKeys($data)));
                    break;
                case 'get_settings':
                	exit(json_encode($s->getSettings()));
                    break;
                case 'get_mail_settings':
                	exit(json_encode($s->getMailSettings($data)));
                    break;
                case 'save_mail_settings':
                	exit(json_encode($s->saveMailSettings($data)));
                    break;
                case 'get_cost_settings':
                	exit(json_encode($s->getCostSettings()));
                    break; 
                case 'save_cost_settings':
                	exit(json_encode($s->saveCostSettings($data)));
                    break;                    	
				default:
					exit(json_encode(['data'=> $data, 'success'=>false, 'error'=> 'Action not found']));
					break;
			}

			break;

		case 'auth':

			$a = new AuthController($DB);

			switch ($action) {
                case 'login':
                	exit(json_encode($a->loginAdmin($data)));
                    break;
                case 'login_member':
                	exit(json_encode($a->loginMember($data)));
                    break;
                case 'reset_password':
                	$sendgrid = new SendGridService($DB);
                	exit(json_encode($a->resetPassword($data, $sendgrid)));
                    break;
				default:
					exit(json_encode(['data'=> $data, 'success'=>false, 'error'=> 'Action not found']));
					break;
			}

			break;

		case 'stripe':

			$s = new StripeService($DB);

			switch ($action) {
                case 'get_plans':
                	exit(json_encode($s->getPlans($data)));
                    break;
                case 'get_cards':
                	exit(json_encode($s->getCards($data)));
                    break;
                case 'get_card_details':
                	exit(json_encode($s->getCardDetails($data)));
                	break;
                case 'save_card_details':
                	exit(json_encode($s->saveCardDetails($data)));
                    break;
                case 'remove_card':
                	exit(json_encode($s->removeCard($data)));
                    break;
				default:
					exit(json_encode(['data'=> $data, 'success'=>false, 'error'=> 'Action not found']));
					break;
			}

			break;

		case 'lob':
			
			$lob = new LobService($DB);
			$lob->deleteOldFiles();

			switch ($action) {
                case 'send_letter':
		        	//it will exist only when user manually try again to submit the letter
                    if ( (!isset($data['recipient']) || empty($data['recipient']['address'])) && !$data['verifyUserCredits'])
                        $data['recipient'] = $lob->getRecipientAddress($data);

		            exit(json_encode($lob->sendLetter($data)));
		            break;
		        case 'get_user_addresses':
		            exit(json_encode($lob->getUserAddresses($data)));
		            break;
		        case 'delete_address':
		            exit(json_encode($lob->deleteAddress($data)));
		            break;
		        case 'upload_additional_files':
		            exit(json_encode($lob->uploadFiles($data)));
		            break;
		        case 'delete_pdf_files':
		            exit(json_encode($lob->deletePDFFiles()));
		            break;
		        case 'create_pdf_files':
		            exit(json_encode($lob->createPDFDocument($data)));
		            break;
		        default:
		            # code...
		            break;
		    }
			break;

		case 'mail_log':
			
			$m = new MailLogController($DB);

			switch ($action) {
		        case 'get_user_logs':
		            exit(json_encode($m->getUserLogs($data)));
		            break;
		        default:
		            # code...
		            break;
		    }
			break;

		case 'credit':

            $c = new CreditsController($DB);

            switch ($action) {
                case 'purchase_credits':
                    exit(json_encode($c->purchaseCredits($data)));
                case 'get_credits_history':
                    exit(json_encode($c->getCreditsHistory($data)));
                case 'get_purchase_history':
                    exit(json_encode($c->getPurchaseHistory($data)));
                case 'get_current_credits':
                    exit(json_encode($c->getCurrentCredits($data)));
                default:
                    exit(json_encode(['data' => $data, 'success' => false, 'error' => 'Action not found']));
            }

		default:
			exit(json_encode(['data'=> $data, 'success'=>false, 'error'=> 'No action found']));
			break;
	}
}
