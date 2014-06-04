<?php
/**
 * User: david
 * Date: 16/05/14
 * Time: 09:39
 */

namespace Controller;

use Models\ModelDefault;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class WaveController extends ControllerDefault {

    public function __construct(){
        //set associated table name (for basic crud functionality)
        parent::__construct('wave');
    }

    /**
     * Additionnal routes
     */
    public function additionnalRoutes(){

        $class = $this->getControllerName();

        $this->setRepository( new \Models\WaveModel( $this->_getPDO() ));

        $this->controller->get('/launch/{id}', "$class:launchAction")->assert('id', '\d+');
    }

    /**
     * @param Application $app
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function updateAction(Application $app, Request $request, $id) {
        if( empty($id) ){
            throw new \Exception('npi id are required!');
        }
        // get request payload content
        $params = json_decode($request->getContent(), true);
        $params[$this->getRepository()->getPrimaryKeyFieldName()] = $id;
        // and create/update wave and related data
        $result = $this->getRepository()->updateWave($params);
        return $app->json( $result );
    }

    /**
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createAction(Application $app, Request $request) {
        // get request payload content
        $params = json_decode($request->getContent(), true);
        // and create/update wave and related data
        $result = $this->getRepository()->updateWave($params);
        return $app->json( $result );
    }

    /**
     * Send data to the external server
     * @param Application $app
     * @param Request $request
     * @param $id
     * @throws \Exception
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function launchAction(Application $app, Request $request, $id){

        //Fetch wave
        $wave = $this->getRepository()->fetchOne($id);

        //Check that configuration is correct
        if( ! isset($app['conf']['exchange']['mobileRootUrl']))
        {
            throw new \Exception('exchange.mobileRootUrl is not set in the config file');
        }
        $url = $app['conf']['exchange']['mobileRootUrl'] . 'exchange/import-wave/' . $id;

        //Check that the wave is in a correct state
        if($wave['wave_status'] != 0)
        {
            throw new \Exception('This wave has already launched');
        }

        //Set wave status to 1 (launched) before sending the data
        $wave['wave_status'] = 1;

        //Init CURL
        $curl = curl_init();

        //Set CURL options
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode(array(
                'stored_cpm_pos' => $this->_fetchTableData('stored_cpm_pos', $id, array(
                    'f_pos_apple_id',
                    'f_legal_name',
                    'f_trade_name',
                    'f_country_name',
                    'f_street',
                    'f_city',
                    'f_postal_code',
                    '_ke_wave',
                )),
                'wave' => $wave,
                'wave_activity' => $this->_fetchTableData('wave_activity', $id),
                'activity' => $this->_fetchTableData('activity'),
                'contact' => $this->_fetchTableData('contact'),
            )),
        ));

        //Send the request & save response to $resp
        $resp = curl_exec($curl);

        //Close request to clear up some resources
        curl_close($curl);

        //Update wave status
//        $this->getRepository()->updateWave(array(
//            '_pk_wave' => $id,
//            'wave_status' => 1,
//        ));
//die($resp);
        //Display what we received
        return $app->json(json_decode($resp, true));
    }

    /**
     * Fetch data from a table
     * @param $table
     * @param $waveId
     * @return array
     */
    protected function _fetchTableData($table, $waveId = null, array $fields=array()){

        if( ! count($fields))
        {
            $repo = new ModelDefault($this->_getPDO(), $table);
            $fields = $repo->getFieldNameList();
        }

        if($waveId)
        {
            $sql = "SELECT ".implode(', ', $fields)." FROM $table p WHERE _ke_wave = ?";
            $values = array($waveId);
        }
        else
        {
            $sql = "SELECT ".implode(', ', $fields)." FROM $table p WHERE 1";
            $values = array();
        }

        //Launch query
        $statement = $this->_getPDO()->prepare($sql);
        $statement->execute($values);
        $result = $statement->fetchAll(\PDO::FETCH_NUM);

        return array(
            'fields' => $fields,
            'data' => $result,
        );
    }
}