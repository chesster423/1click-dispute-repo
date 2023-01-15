<?php
/**
 * 
 */
class SettingController extends BaseController
{

    public function saveKeys($request) {

        $delete_query = $this->conn->ExecuteQuery("DELETE FROM settings WHERE name <> 'tos' AND name <> 'mail_settings'");
        $data = [];

        if ($delete_query) {

            foreach ($request as $key => $value) {
                if (isset($value->name)) {
                    $data[] = [
                        'name' => $value->name,
                        'description' => $value->description,
                        'production_key' => $value->production_key,
                        'development_key' => $value->development_key,
                        'dev_active' => ($value->dev_active) ? 1 : 0,
                    ];
                }

            }

            return parent::create_bulk($data);
        }

        return $this->response(false, "An error occured", $request);

    }


    public function getSettings() {

        $data = [
            'stripe' => [
                'name' => 'stripe',
                'description' => 'Stripe Key',
                'production_key' => null,
                'development_key' => null,
                'dev_active' => 0,
            ],
            'sendgrid' => [
                'name' => 'sendgrid',
                'description' => 'Sendgrid Key',
                'production_key' => null,
                'development_key' => null,
                'dev_active' => 0,
            ],
            'lob' => [
                'name' => 'lob',
                'description' => 'Lob Key',
                'production_key' => null,
                'development_key' => null,
                'dev_active' => 0,
            ]
        ];

        $settings = $this->conn->GetRows("SELECT * FROM settings ORDER BY id ASC");

        foreach ($settings as $key => $value) {
            $data[$value['name']]['production_key'] = $value['production_key'];
            $data[$value['name']]['development_key'] = $value['development_key'];
            $data[$value['name']]['dev_active'] = ($value['dev_active']) ? true : false;
        }

        return $this->response(true, 'Settings retrieved', $data);

    }


    public function getMailSettings($request = []) {

        $mail_settings = $this->conn->GetDataFromDb("SELECT * FROM settings WHERE name = 'mail_settings'");

        if ($mail_settings) {
            return $this->response(true, 'Mail settings retrieved', json_decode($mail_settings['content'], true));
        }

        return $this->response(false, 'No Mail settings found');

    }

    public function saveMailSettings($request) {

        $delete_query = $this->conn->ExecuteQuery("DELETE FROM settings WHERE name = 'mail_settings'");

        $values = "('mail_settings', '-', '-', '-', '" . $this->conn->escape( json_encode($request['content']) ) . "')";

        $query = "INSERT INTO settings 
        (name, production_key, development_key, description, content)
        VALUES $values";

        if ($this->conn->ExecuteQuery($query)) {
            return $this->response(true, "Mail settings saved!");
        }

        return $this->response(false, 'An error occured');

    }

    public function getCostSettings() {

        $data = [];

        $query = "SELECT * FROM cost_settings ORDER BY id ASC LIMIT 1";

        $data = $this->conn->GetDataFromDb($query);

        if(!$data) {
            $data = [
                'initialCost'    => 0,
                'costPerPage'    => 0,
                'certifiedCost'  => 0,
                'additionalCost' => 0
            ];
        }

        return $this->response(true, 'Cost Settings retrieved', $data);

    }

    public function saveCostSettings($request) {

        $initialCost = $this->conn->escape($request['initialCost']);
        $costPerPage = $this->conn->escape($request['costPerPage']);
        $certifiedCost = $this->conn->escape($request['certifiedCost']);
        $additionalCost = $this->conn->escape($request['additionalCost']);

        $query = "UPDATE cost_settings 
        SET 
        initialCost = '$initialCost', 
        costPerPage = '$costPerPage', 
        certifiedCost = '$certifiedCost', 
        additionalCost = '$additionalCost'
        WHERE id = 1";

        if ($this->conn->ExecuteQuery($query)) {
            return $this->response(true, "Cost settings saved!");
        }

        return $this->response(false, 'An error occured. Please enter correct values.');

    }

}