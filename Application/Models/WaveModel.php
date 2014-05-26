<?php
/**
 * User: david
 * Date: 16/05/14
 * Time: 14:26
 */

namespace Models;

class WaveModel extends ModelDefault{

    public function __construct($pdo_instance){
        parent::__construct($pdo_instance, 'wave');
        /**
         * warning : just because activity table has field with json data formated
         * don't forget json treatment before json encode
         */
        $this->jsonFields = array('activ_config');
    }

    public function updateWave( $data ){

        $idWave = false;

        //check if wave exist => insert or update
        if( !isset($data['_pk_wave']) || empty($data['_pk_wave']) ){
            $idWave = $this->create($data);
        }else{
            $this->update($data);
            $idWave = $this->cleanData($data['_pk_wave']);
        }

        //get all activity for current wave
        $sql = 'SELECT * FROM waveactivity WHERE _ke_wave = '.$this->cleanData($idWave);
        $statement = $this->query($sql);
        $waveactivities = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $createWaveActivity_dataString = '';
        $keyActivityExist = array();

        //build db activities collection for current wave
        foreach( $waveactivities as $value){
            $keyActivityExist[] = $value['_ke_activity'];
        }

        //waves activities data send treatment
        if( isset($data['activities']) && 0 < count($data['activities']) ){
            foreach( $data['activities'] as $key => $activity){
                //add data at create or updated wave activity formated string
                $createWaveActivity_dataString .= '('
                    .$this->cleanData($idWave).','
                    .$this->cleanData($activity['_ke_activity']).','
                    .$this->cleanData($activity['order']).
                ') ,';

                //search position activity in wave activity collection
                $positionActivity = array_search($activity['_ke_activity'], $keyActivityExist, false);
                if( false !== $positionActivity ){
                    //unset updated data from futur deleted list activity
                    unset( $keyActivityExist[$positionActivity] );
                }
            }
            //delete the last , from data formated string
            $createWaveActivity_dataString = rtrim($createWaveActivity_dataString, ',');
        }

        //and if data => persist in db
        if( !empty($createWaveActivity_dataString) ){
            $createWaveActivityStatement = 'INSERT INTO `waveactivity`
                                            (
                                            `_ke_wave`,
                                            `_ke_activity`,
                                            `waveactiv_order`
                                            )
                                        VALUES '.$createWaveActivity_dataString.' ON DUPLICATE KEY UPDATE `waveactiv_order` = VALUES(`waveactiv_order`)';
            $this->query($createWaveActivityStatement);
        }

        //check if wave  activity has been deleted
        if( 0 < count($keyActivityExist) ){
            $deleteWaveActivityStatement = 'DELETE FROM `waveactivity` WHERE `_ke_wave` =  '.$idWave.' AND _ke_activity IN ( '.implode(',', $keyActivityExist). ' )';
            $this->query($deleteWaveActivityStatement);
        }

        return $this->fetchOne($idWave);
    }

    public function fetchOne( $id ){
        $sql = 'SELECT * FROM wave w
                    LEFT JOIN waveactivity wa ON wa._ke_wave = w._pk_wave
                    LEFT JOIN activity a ON wa._ke_activity = a._pk_activity
                WHERE _pk_wave = '.$this->cleanData($id).' ORDER BY waveactiv_order';
        /*@var $statement \PDOStatement*/
        $statement = $this->query($sql);
        return $this->_formatDbJsonFields( $statement->fetchAll(\PDO::FETCH_ASSOC) );
    }

    protected function _formatDbJsonFields( $data ){
        $result = array( 'activities' => array() );
        //if data content at least one result
        if( 0 < count($data) ){
            //feed array result data with wave data
            $wavefield = $this->getFieldNameList();
            foreach( $wavefield as $fieldName ){
                $result[ $fieldName ] = $data[0][ $fieldName ];
            }
            $result = $this->_prepareDataForJsonify($result);

            //add activity related wave info
            foreach( $data as $activitie){
                if( null !== $activitie['_ke_wave'] ){
                    $result['activities'][] = array(
                        '_ke_activity' => $activitie['_ke_activity'],
                        '_ke_wave' => $activitie['_ke_wave'],
                        'order' => $activitie['waveactiv_order']
                    );
                }
            }
            $result['activities'] = $this->_prepareDataSetForJsonify($result['activities']);

            return $result;
        }
    }
}
