<?php
use Dompdf\Dompdf;

class LobService
{

   protected $db = null;
   protected $api_key = null;
   private $client = null;
   private $initialCost = .8;
   private $costPerPage = .12;
   private $certifiedCost = 5.97;
   private $additionalCost = 2.35;

   public function __construct($db)
   {
      $this->db = $db;
      $this->getCostSettings();
   }

   public function getClient($request) {

      $key = null;

      $query = "SELECT id, name, dev_active, production_key, development_key FROM settings WHERE name = 'lob'";

      $data = $this->db->GetDataFromDb($query);

      $key = $data['production_key'];

      if ($data['dev_active']) {
         $key = $data['development_key'];
      }

      $this->client = new \Lob\Lob( $key );

   }

   public function deleteOldFiles() {

      $whitelist = array(
          '127.0.0.1',
          '::1'
      );

      if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) { 

         //let's delete files older than 5 days
         $sql = $this->db->ExecuteQuery("SELECT id, filename FROM files_log WHERE createdAt < DATE_ADD(NOW(), INTERVAL -5 DAY) LIMIT 100");
         while($data = $sql->fetch_assoc()) {
            if (strpos($data['filename'], '.pdf') === false)
               $data['filename'] .= '.pdf';

            if (file_exists('/srv/app.1clickdispute.com/files/dispute_aid_letters/'.$data['filename']))
               unlink('/srv/app.1clickdispute.com/files/dispute_aid_letters/'.$data['filename']);

            $this->db->ExecuteQuery('DELETE FROM files_log WHERE id="'.$data['id'].'"');
         }

         $this->deletePDFFiles();

         $uploaded_files = (scandir('uploaded_files')) ? scandir('uploaded_files') : [];

         if ($uploaded_files) {

            foreach ($uploaded_files as $key => $value) {

               $file = 'uploaded_files/'.$value;

               if (is_file($file)) {
                  if (time() - filemtime($file) >= 60 * 60 * 24 * 3) { // 3 days
                    unlink($file);
                  }
               }
            }
         }
      }

   }

   public function deletePDFFiles() {

      $pdf_files = (scandir('files/dispute_aid_letters/')) ? scandir('files/dispute_aid_letters/') : [];
      $deleted_files = [];

      if ($pdf_files) {

         foreach ($pdf_files as $key => $value) {

            $file = 'files/dispute_aid_letters/'.$value;

            if (is_file($file)) {               
               if (time() - filemtime($file) >= 60 * 60 * 24 * 14) { // 14 days                  
                  unlink($file);
                  $deleted_files[] = $file;
               }
            }
         }
      }

      return ['success'=>true, 'msg'=>'PDF Files deleted', 'data' => $deleted_files];
   }

   public function uploadFiles($request) {

      $file = [];

      if (isset($_FILES['attachments'])) {

         $file = $_FILES['attachments'];

         $file['new_name'] = uniqid()."-".$file['name'][0];

         $tmp = isset($file['tmp_name'][0]) ? $file['tmp_name'][0] : null;

         // return false if file is greater than 1MB
         if (filesize($tmp) > 1000000) {
            return ['success'=>false, 'msg'=>'Max allowed size is: 1MB', 'data' => $file];
         }

         if ($tmp && move_uploaded_file($tmp, 'uploaded_files/'.$file['new_name'])) {
            return ['success'=>true, 'msg'=>'File saved', 'data' => $file, 'filename' => $file['new_name']];
         }
         
      }

      return ['success'=>false, 'msg'=>'An error occured when saving file', 'data' => $file];

   }

   public function removeImages($pdf_content) {

      $content = $pdf_content;

      $content = preg_replace('/<img style="max-width: 573px;width: 70%; height:auto;"[^>]+>/', '', $pdf_content);

      return $content;

   }

   public function prependImages($pdf_content, $images) {

      sort($images);

      $imgs = '';
      $base_url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

      $whitelist = array(
          '127.0.0.1',
          '::1'
      );

      $src = 'http://localhost/dominique-app/uploaded_files/';

      if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
         $src = "https://".str_replace('/action.php?entity=lob&action=send_letter', '', $base_url)."/uploaded_files/";
      }

      foreach ($images as $key => $value) {

         $img = $src.$value;

         $imgs .= '<div class="pageBreak"><img src="'.$img.'"  alt="'.$value.'" style="max-width: 500px;width: fit-content; height:650px;" /></div>';
         // $imgs .= '<div class="pageBreak" style="page-break-after:always; display: inline-block;"><img src="'.$img.'"  alt="'.$value.'" style="max-width: 573px;width: fit-content; height:auto;" /></div>';

      }

      $pdf_content = str_replace('<style type="text/css">', $imgs.'<style type="text/css">', $pdf_content);

      return $pdf_content;

   }

   public function getRecipientAddress($request) {

      $data = [];

      if (isset($request['pdf']) && (strpos(html_entity_decode($request['pdf']), '{{30daycra_start}}') !== false || strpos(html_entity_decode($request['pdf']), '<30daycra>') !== false)) {
         if (strpos(html_entity_decode($request['pdf']), '{{30daycra_start}}') !== false)
            $tags = ['{{30daycra_start}}', '{{30daycra_end}}'];
         else
            $tags = ['<30daycra>', '</30daycra>'];

         $pdf = html_entity_decode($request['pdf']);
         $recipient = explode($tags[0], $pdf);
         $recipient = explode($tags[1], $recipient[1]);
         $recipient = $recipient[0];
         $recipient = explode('<br />', $recipient);

         $startIndex = 1;

         if ($recipient[0] != '')
            $startIndex = 0;

         $address_data[0] = strip_tags($recipient[$startIndex]);
         $address_data[1] = strip_tags($recipient[($startIndex+1)]);
         $address_data[2] = strip_tags($recipient[($startIndex+2)]);
      } else {
         $reg = preg_match_all('/(<p>)(.*?)(<\/p>)/', $request['pdf'], $matches);

         if (isset($matches[2][1])) {
            $address_data = explode('<br />', $matches[2][1]);
            $address_data[0] = isset($address_data[0]) ? strip_tags($address_data[0]) : null;
            $address_data[1] = isset($address_data[1]) ? strip_tags($address_data[1]) : null;
            $address_data[2] = isset($address_data[2]) ? strip_tags($address_data[2]) : null;
         }
      }

   // Getting the recipient's name
      if (isset($address_data[0])) {
         $name = explode(' ', $address_data[0]);

         $data['firstName'] = isset($name[0]) ? $name[0] : '';
         $data['lastName'] = isset($name[1]) ? str_replace($data['firstName'], '', $address_data[0]) : '';
      }

      $data['address'] = $address_data[1];

   // Getting the recipient's city, state, zip
      if (isset($address_data[2])) {
         $address = explode(', ', $address_data[2]);
         $data['city'] = $address[0];

         $address = explode(' ', $address[1]);

         $data['state'] = isset($address[0]) ? $address[0] : null;
         $data['zip'] = isset($address[1]) ? $address[1] : null;

         $data['country'] = 'United States';
      }

      if (preg_match('/Experian|TransUnion|Equifax/', $request['pdf'], $matches)) {

         if (isset($matches[0])) {
            $data['fax'] = $matches[0];
         }

      }

      return $data;

   }

   public function getUserAddresses($request) {

      $this->getClient($request);

      $params = ['limit' => 100];

      if (isset($request['after'])) {

         preg_match('/(after=)+(.+)/', $request['after'], $matches);

         if (isset($matches[2])) {
            $params['after'] = $matches[2];
         }

      }

      if (isset($request['before'])) {

         preg_match('/(before=)+(.+)/', $request['before'], $matches);

         if (isset($matches[2])) {
            $params['before'] = $matches[2];
         }
         
      }

      $data = $this->client->addresses()->all($params);

      return ['success'=>true, 'msg'=>'Address retrieved', 'data' => $data];

   }

   public function deleteAddress($request) {

      if (isset($request['address_id']) && isset($request['user_id'])) {

         $this->getClient($request);

         $address_id = $request['address_id'];

         $delete_address = $this->client->addresses()->delete($address_id);

         if ($delete_address) {
            return ['success'=>true, 'msg'=>'Address successfully deleted.', 'data' => $delete_address];
         }

         return ['success'=>false, 'msg'=>'Failed to delete address'];
      }

      return ['success'=>false, 'msg'=>'Some parameters are missing'];
   }

   public function savePDFDocument($pdf_content, $pdf_name) {

      try {

         $filepath = str_replace('services', '', __DIR__);

         $content = $pdf_content;

         $html = preg_replace('/<style((.|\n|\r)*?)<\/style>/', '', $content);
         $html = preg_replace('/<input type=\"hidden".*>/', '', $html);
         $html = str_replace('style="page-break-after:always; display: inline-block;"', '', $html);
         $html = str_replace('style="max-width: 573px;width: fit-content; height:auto;"', 'style="max-width: 573px;width: fit-content; height: 650px;"', $html);
  
         $dompdf = new Dompdf();
         $dompdf->set_option('isHtml5ParserEnabled', true);
         $dompdf->set_option('isRemoteEnabled', true); 

         $dompdf->loadHtml($html); 

         $dompdf->render();

         $output = $dompdf->output();

         file_put_contents($filepath.'files/dispute_aid_letters/'.$pdf_name.".pdf", $output);

         $totalPages = $dompdf->get_canvas()->get_page_count();

         $cost = $this->calculateDocumentCost($totalPages);

         $response = [
            'html_pdf_output' => $html,
            'total_pages'     => $totalPages,
            'cost'            => (float)$cost
         ];
         
         return $response;
         
      } catch (Exception $e) {

         $response = [
            'error' => $e,
            'success' => false,
         ];
         
         return $response;
         
      }

   }

   public function base_url(){

      $request_uri = sprintf(
         "%s://%s%s",
         isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
         $_SERVER['SERVER_NAME'],
         $_SERVER['REQUEST_URI']
      );

      $base_url = preg_replace('/(\/action\.php.+action\=.+)/', '', $request_uri);

      return $base_url;

   }

   public function createPDFDocument($request) {

      if (!$request['address']['state']) {
            return [
            'success'      => false, 
            'msg'          => 'Some parameters are missing', 
            'description'  => 'address_state is required',
            'data' => 
            [
               'from' => [
                  'name' => $request['address']['firstName']." ".$request['address']['lastName'],
               ]
            ],
            'request' => $request
         ];
      }

      if ($request['address']['state'] == 'U.S. Virgin Islands') {
         $request['address']['state'] = 'Virgin Islands';
      }

      $file_content = null;      

      if (isset($request['options']['restrict_identity_documents']) && $request['options']['restrict_identity_documents'] == 'true') {
         $request['pdf'] = $this->removeImages($request['pdf']);
      }

      if (isset($request['options']['upload_additional_documentation']) && $request['options']['upload_additional_documentation'] == 'true' && isset($request['uploaded_files'])) {
         $request['pdf'] = $this->prependImages($request['pdf'], $request['uploaded_files']);
      }

      $request['pdf'] = str_replace('<img style="max-width: 573px;width: 70%; height:auto;"', '<img style="max-width: 573px;width: fit-content; height:auto;"', $request['pdf']);
      $to_name = (isset($request['recipient']['firstName']) && isset($request['recipient']['lastName'])) ?  $request['recipient']['firstName']." ".$request['recipient']['lastName'] : null;

      // We will save the file in our server and provide them the link instead of sending them the whole HTML code.
      // Reason: Lob won't accept char length >= 10000
      // if (strlen($request['pdf']) >= 10000) {
      // }
      /*UPDATE Aug 3, 2021: Always save the file in our server*/
      $filename = $request['address']['firstName']."-".$request['address']['lastName']."-".$request['letterID'];

      $document = $this->savePDFDocument($request['pdf'], $filename);

      if (isset($document['success']) && $document['success'] == false) {
         return ['success' => false, 'msg'=> 'Document failed.', 'data'=> $document];
      }

      $file_content = $this->base_url()."/files/dispute_aid_letters/".$filename.".pdf";
      $fax = isset($request['recipient']['fax']) ? $request['recipient']['fax'] : null;

      $this->db->ExecuteQuery("INSERT INTO files_log (filename,createdAt) VALUES ('$filename',NOW())");

      if($file_content) {
         return [
            'success'   => true, 
            'msg'       => 'Letter successfully created', 
            'file_url'  => $file_content, 
            'fax'       => $fax, 
            'from'      => [
               'name'   => $request['address']['firstName']." ".$request['address']['lastName']
            ],
            'to'        => $to_name,
            'document'  => $document,
            'letterID' => $request['letterID']
         ];
      }

      return ['success' => false, 'msg'=> 'Document failed.'];
      
   }

   public function verifyUserCredits($request) {

      $totalCreditCost = (int)$this->db->escape($request['totalCreditCost']);
      $userId = $this->db->escape($request['user_id']);

      $user = $this->db->GetDataFromDb("SELECT id, currentCredits FROM users WHERE id = '$userId'");

      if ($totalCreditCost > (int)$user['currentCredits']) {
         return [
            'success'   => false, 
            'msg'       => 'Insufficient credits. Current Credits: ' . $user['currentCredits'],
            'error_type'=> 'payment_declined',
            'credits'   => $user['currentCredits'],
         ];
      }

      return [
         'success'   => true, 
         'msg'       => 'User credits verified',
         'credits'   => $user['currentCredits'],
      ];

   }

   public function sendLetter($request) {

      try {

         //Verify the over-all total cost first if credits are enough 
         if (isset($request['verifyUserCredits']) && $request['verifyUserCredits'] == true) {
            
            return $this->verifyUserCredits($request);

         }

         $this->getClient($request);

         $letter_data = [];

         $totalCost = (float)0;
         $userId = $this->db->escape($request['user_id']);
         $letterID = $this->db->escape($request['letterID']);
         $totalPages = (int)$request['totalPages'];    
         $file = $request['fileUrl'];

         // For localhost testing. Lob cannot retrieve the PDF file from the local server.
         if(in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', "::1"))){
             $file = 'http://www.africau.edu/images/default/sample.pdf';
         }

         $isCertified = false;

         if (isset($request['isCertified']) && $request['isCertified'] == 'yes') {

            $isCertified = true;

            $letter_data['mail_type'] = 'usps_first_class';

            if (isset($request['options']['with_certified_return_receipt']) && $request['options']['with_certified_return_receipt'] == true) {              
               $letter_data['extra_service'] = 'certified_return_receipt';
            }else{
               $letter_data['extra_service'] = 'certified';
            }

         }

         $totalCost = $this->calculateDocumentCost($totalPages, $request['isCertified']);

         $verifyCost = [
            'user_id'         => $userId,
            'totalCreditCost' => (float)$totalCost,
         ];

         // Verify individual letter costs. This is a safety net if someone bypass the over-all cost verification.
         $verify = $this->verifyUserCredits($verifyCost);

         if (!$verify['success']) {
            return [
               'success'      => false, 
               'msg'          => 'Insufficient credits. Current Credits: ' . $verify['currentCredits'], 
               'file_url'     => $file, 
               'letterID'     => $request['letterID']
            ];
         }

         $to_name = (isset($request['recipient']['firstName']) && isset($request['recipient']['lastName'])) ?  $request['recipient']['firstName']." ".$request['recipient']['lastName'] : null;

         $to_address = [
            'to[name]'              => $to_name,
            'to[address_line1]'     => isset($request['recipient']['address']) ? $request['recipient']['address'] : null,
            'to[address_line2]'     => '',
            'to[address_city]'      => isset($request['recipient']['city']) ? $request['recipient']['city'] : null,
            'to[address_zip]'       => isset($request['recipient']['zip']) ? $request['recipient']['zip'] : null,
            'to[address_state]'     => isset($request['recipient']['state']) ? $request['recipient']['state'] : 'Not specified',
         ];

         $from_address = [
            'from[name]'            => $request['address']['firstName']." ".$request['address']['lastName'],
            'from[address_line1]'   => $request['address']['address'],
            'from[address_line2]'   => '',
            'from[address_city]'    => $request['address']['city'],
            'from[address_zip]'     => $request['address']['zip'],
            'from[address_state]'   => ($request['address']['state']) ? $request['address']['state'] : 'Not specified'
         ];

         // Test data for wrong state zip.
         // $to_address = [
         //    'to[name]'              => $to_name,
         //    'to[address_line1]'     => isset($request['recipient']['address']) ? $request['recipient']['address'] : null,
         //    'to[address_line2]'     => '',
         //    'to[address_city]'      => isset($request['recipient']['city']) ? $request['recipient']['city'] : null,
         //    'to[address_zip]'       => '9999999',
         //    'to[address_state]'     => 'ZZA',
         // ];

         // $from_address = [
         //    'from[name]'            => $request['address']['firstName']." ".$request['address']['lastName'],
         //    'from[address_line1]'   => $request['address']['address'],
         //    'from[address_line2]'   => '',
         //    'from[address_city]'    => $request['address']['city'],
         //    'from[address_zip]'     => '000000',
         //    'from[address_state]'   => 'BEEEE'
         // ];

         $letter_data = [
            'file'               => $file,
            'description'        => 'Sending letter via Lob',
            'color'              => false,
            'double_sided'       => true,
            'address_placement'  => 'insert_blank_page',
            'return_envelope'    => false,
            'mail_type'          => (isset($request['options']['mail_type'])) ? $request['options']['mail_type'] : 'usps_standard',
         ];

         if (isset($request['isCertified']) && $request['isCertified'] == 'yes') {
            $letter_data['mail_type'] = 'usps_first_class';
         }

         $fax = isset($request['recipient']['fax']) ? $request['recipient']['fax'] : null;

         $letter_data = array_merge_recursive($letter_data, $to_address, $from_address);

         $letter = null;
         $letter = $this->client->letters()->create($letter_data);

         if ($letter) {
             $letterType = ($isCertified) ? 'certified' : 'standard';
             $sender = ucwords(strtolower($request['address']['firstName']." ".$request['address']['lastName'])) . ' - ' . $letter['id'];

             $query = "
                INSERT INTO credits_history 
                    (userID, creditsAmount, item, actionDate) 
                VALUES 
                    ('$userId', '$totalCost', '$letterType letter printed ($sender)', NOW())";

             $this->db->ExecuteQuery($query);

            $query = "INSERT INTO mail_logs 
            (transaction_code, mail_system, user_id, letterId, pages, totalCost, isCertified, transaction_name, success, log_msg) VALUES 
            (
            '" . $this->db->escape($request['transaction_code']) . "', 
            'lob',
            '$userId', 
            '$letterID', 
            '$totalPages',
            '$totalCost',
            '" . (($isCertified) ? 1 : 0) . "',
            'Create letters', 
            '1', 
            '" . json_encode($letter) . "')";
            $this->db->ExecuteQuery($query);

            $this->db->ExecuteQuery("UPDATE users SET currentCredits = currentCredits-$totalCost WHERE id = '$userId'");

            return [
               'success'      => true, 
               'msg'          => 'Sent', 
               'data'         => $letter, 
               'file_url'     => $file, 
               'fax'          => $fax, 
               'letterID'     => $letterID,
               'totalCost'    => (float)$totalCost,
               'isCertified'  => $isCertified
            ];
         }

         return [
            'success'      => false, 
            'msg'          => 'An error occured', 
            'data'         => $letter, 
            'file_url'     => $file, 
            'fax'          => $fax, 
            'letterID'     => $letterID,
            'totalCost'    => (float)$totalCost,
            'isCertified'  => $isCertified,
         ];

      } catch (Exception $e) {

         return [
            'success'      => false,
            'msg'  => $e->getMessage(),
            "from"         => $from_address, 
            "to"           => $to_address,
            "request"      => $request,
            'letterID'     => $letterID
         ];
      }

   }

   public function calculateDocumentCost($totalPages, $isCertified = null) {

      $cost = 0;

      $initialCost = $this->initialCost;

      if ($isCertified == 'yes') {
         // certified cost 5.97
         $initialCost = $this->$certifiedCost;
      }

      // .8 = first page
      // then totalPages x .12
      $cost = $initialCost + (($totalPages - 1) * $this->costPerPage);

      if ($totalPages >= 12) {
         $cost += $this->$additionalCost;
      }

      return number_format((float)$cost, 2);
   }

   public function getCostSettings(){

      $query = "SELECT * FROM cost_settings ORDER BY id ASC LIMIT 1";

      $data = $this->db->GetDataFromDb($query);

      if ($data) {
         $this->initialCost = (float)$data['initialCost'];
         $this->certifiedCost = (float)$data['certifiedCost'];
         $this->costPerPage = (float)$data['costPerPage'];
         $this->additionalCost = (float)$data['additionalCost'];
      }

   }

}
