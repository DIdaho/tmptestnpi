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

class CpmPosController extends ControllerDefault {

    public function __construct(){
        parent::__construct('cpm_pos');
    }

    public function connect(Application $app) {
        $this->_setApp($app);
        $this->setRepository();

        //Register CPM POS controller
        $app['cpm-pos.controller'] = $this;
        $this->controller->post("/", "cpm-pos.controller:filterAction");
        $this->controller->post("/add-pos-to-wave/{id}", "cpm-pos.controller:addPosToWaveAction")->assert('id', '\d+');
        $this->controller->get("/dictionary", "cpm-pos.controller:dictionaryAction");

        return $this->controller;
    }

    /**
     * Filtered list of POS
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function filterAction(Application $app, Request $request){
        ini_set('memory_limit', '256M');
        $params = json_decode($request->getContent(), true);

        //Fields to return
        $map = array(
            'pos_apple_id' => 'p.f_pos_apple_id',
            'pos_hq_id' => 'f_hq_apple_id',
            'pos_contract_id' => 'f_contract_id',
            'pos_name' => 'f_legal_name',
            'pos_tradename' => 'f_trade_name',
            'pos_sales_id' => 'f_primary_sales_org',
            'pos_sales_name' => 'f_primary_sales_org_name',
            'pos_loc_region' => 'f_region',
            'pos_loc_country' => 'f_country_name',
            'pos_loc_street' => 'f_street',
            'pos_loc_city' => 'f_city',
            'pos_loc_postal_code' => 'f_postal_code',
            'pos_rtm' => 'f_rtm_primary',
            'isLinked' => "0",
        );

        //Work on fields
        array_walk($map, function(&$v, $k){$v = "$v as $k";});
        $fields = implode(', ', $map);

        //Compute where options
        $where = array(
            'p.f_pos_apple_id>9999',    //limitation requested by Ruchir
            'p.f_hq_apple_id>9999',     //limitation requested by Ruchir
            'p.f_contract_id>9999',     //limitation requested by Ruchir
        );
        $values = array();

        //Where RTM & Program
        $checkboxes = array(
            'rtm' => 'f_rtm_primary',
            'program' => 'f_program',
            'region' => 'f_region',
            'countries' => 'f_country_name',
            'salesorgs' => 'f_primary_sales_org_name',
        );
        foreach($checkboxes as $key=>$field)
        {
            $keys = array();
            if(isset($params[$key]) && is_array($params[$key]))
            {
                $keys = array_keys(array_filter($params[$key]));
                if(count($keys))
                {
                    $where[] = $field . ' IN (' . rtrim(str_repeat('?, ', count($keys)), ', ') . ')';
                }
            }
            else if(isset($params[$key])){
                $where[] = "$field = ?";
                $keys = array($params[$key]);
            }
            $values = array_merge($values, $keys);
        }

        if(isset($params['spotlight']))
        {
            $where[] = "(p.f_pos_apple_id LIKE ? OR p.f_legal_name LIKE ? OR p.f_trade_name LIKE ?)";
            $values = array_merge($values, array_fill(0, 3, '%' . $params['spotlight'] . '%'));
        }

        //Compute WHERE
        $where = implode(' AND ', $where);

        $sql = "
        SELECT %s
        FROM cpm_pos p
        LEFT JOIN cpm_sfo s ON s.f_pos_apple_id = p.f_pos_apple_id
        WHERE %s
        # LIMIT 100
        ";
        $sql = sprintf($sql, $fields, $where);

        //Launch query
        $statement = $this->_getPDO()->prepare($sql);
        $statement->execute($values);
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $app->json(array(
            'sql' => $sql,
            'results' => $result,
        ));
    }

    /**
     * Returns configuration information (dictionaries)
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function dictionaryAction(Application $app){
        //Programs
        $sql = "SELECT DISTINCT f_program as name FROM cpm_sfo s LEFT JOIN cpm_pos p ON s.f_pos_apple_id = p.f_pos_apple_id  WHERE p.f_pos_apple_id>9999 ORDER BY f_program";
        $statement = $this->_getPDO()->query($sql);
        $programs = $statement->fetchAll(\PDO::FETCH_ASSOC);

        //Regions
        $sql = "SELECT DISTINCT f_region as name FROM cpm_pos WHERE f_region!='' ORDER BY f_region";
        $statement = $this->_getPDO()->query($sql);
        $regions = $statement->fetchAll(\PDO::FETCH_ASSOC);

        //RTMs
        $sql = "SELECT DISTINCT f_rtm_primary as name FROM cpm_pos WHERE f_rtm_primary!='' AND f_pos_apple_id>9999 ORDER BY f_rtm_primary";
        $statement = $this->_getPDO()->query($sql);
        $rtms = $statement->fetchAll(\PDO::FETCH_ASSOC);

        //Countries
        $sql = "SELECT DISTINCT f_country as id, f_country_name as name FROM cpm_pos WHERE f_country!='00' ORDER BY f_country_name";
        $statement = $this->_getPDO()->query($sql);
        $countries = $statement->fetchAll(\PDO::FETCH_ASSOC);

        //Countries
        $sql = "SELECT DISTINCT f_primary_sales_org_name as name FROM cpm_pos WHERE f_primary_sales_org_name NOT LIKE '%AppleCare%' AND f_primary_sales_org_name!='' ORDER BY f_primary_sales_org_name";
        $statement = $this->_getPDO()->query($sql);
        $sales = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $app->json(array(
            'rtms' => array_filter($rtms),
            'programs' => $programs,
            'regions' => array_filter($regions),
            'salesorg' => array_filter($sales),
            'countries' => array_filter($countries),
        ));
    }

    /**
     * Add POS to a wave     *
     * @param Application $app
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addPosToWaveAction(Application $app, Request $request, $id){

        //Get params (list of Apple IDs to add)
        $appleIds = json_decode($request->getContent(), true);

        $this->_store('cpm_pos', 'stored_cpm_pos', $id, $appleIds);
        $this->_store('cpm_pos_rule', 'stored_cpm_pos_rule', $id, $appleIds, 'f_apple_id');
        $this->_store('cpm_sfo', 'stored_cpm_sfo', $id, $appleIds);

        //Return inserted lines

        return $app->json(null);
    }

    /**
     * Store data from a CPM table to a storage table
     * @param $originTable
     * @param $targetTable
     * @param $waveId
     * @param array $appleIds
     */
    protected function _store($originTable, $targetTable, $waveId, array $appleIds, $field='f_pos_apple_id'){
        //Get field name list
        $repo = new \Models\ModelDefault($this->_getPDO(), $originTable);
        $fields = $repo->getFieldNameList();

        //Create prepared request
        $sql = "
            INSERT IGNORE INTO $targetTable (_ke_wave, ".implode(',', $fields).")
            SELECT
                ?, ".implode(',', $fields)."
            FROM $originTable
            WHERE
                $field IN (" . rtrim(str_repeat('?, ', count($appleIds)), ', ') . ");
        ";

        //Launch query
        $statement = $this->_getPDO()->prepare($sql);
        $statement->execute(array_merge(array($waveId), $appleIds));
    }
}