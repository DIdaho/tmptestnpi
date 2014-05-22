function ctrlMobile($scope, $http, $location){
    $scope.list = [
        {id:'91728', name: 'GDA', city: 'MAUGUIO', zip: '34130'},
        {id:'878036', name: 'A.C.T.I. MAC', city: 'MONDEVILLE', zip: '14120'},
        {id:'874373', name: 'INTER-ACTIF', city: 'ROISSY-EN-FRANCE', zip: '95700'},
        {id:'778785', name: 'GDA', city: 'NIMES', zip: '30900'},
        {id:'778526', name: 'ICONCEPT', city: 'LIMOGES', zip: '87000'},
        {id:'750885', name: 'DXM', city: 'SAINT-MALO', zip: '35400'},
        {id:'727908', name: 'ICONCEPT', city: 'BIGANOS', zip: '33380'},
        {id:'714990', name: 'DXM', city: 'NANTES', zip: '44000'},
        {id:'709809', name: 'LDK2', city: 'MULHOUSE', zip: '68100'},
        {id:'691264', name: 'EPHESUS', city: 'GRENOBLE', zip: '38000'},
        {id:'670657', name: 'MAC & CO', city: 'LE MANS', zip: '72000'},
        {id:'657999', name: 'ANDROMAC', city: 'CABRIES', zip: '13480'},
        {id:'630135', name: 'ISWITCH', city: 'COQUELLES', zip: '62902'},
        {id:'609109', name: 'ICONCEPT', city: 'BAYONNE', zip: '64100'},
        {id:'588626', name: 'ISWITCH', city: 'ARRAS', zip: '62000'},
        {id:'554952', name: 'DXM', city: 'SAINT-GREGOIRE', zip: '35760'},
        {id:'549029', name: 'GDA', city: 'PERPIGNAN', zip: '66000'},
        {id:'542127', name: 'ARCAN IDF', city: 'THIONVILLE', zip: '57100'},
        {id:'53886', name: 'MICRO COMPUTER SERVICES M C S', city: 'NICE', zip: '06000'},
        {id:'531860', name: 'MAC & CO', city: 'VINEUIL', zip: '41350'},
        {id:'529907', name: 'INTER-ACTIF', city: 'THILLOIS', zip: '51370'},
        {id:'456985', name: 'SYMBIOSE INFORMATIQUE', city: 'SAINT-BRIEUC', zip: '22000'},
        {id:'456865', name: 'A.C.T.I. MAC', city: 'LE HAVRE', zip: '76600'},
        {id:'456858', name: 'I-ARTIFICIELLE', city: 'COMPIEGNE', zip: '60200'},
        {id:'451363', name: 'INFORMATIQUE ET PREVENTION', city: 'POITIERS', zip: '86000'},
        {id:'410296', name: 'INFORMATIQUE ET PREVENTION', city: 'LA ROCHELLE', zip: '17000'},
        {id:'405104', name: 'CORSE INFORMATIQUE DEVELOPPEMENT', city: 'BASTIA', zip: '20200'},
        {id:'404029', name: 'SYMBIOSE INFORMATIQUE', city: 'LORIENT', zip: '56100'},
        {id:'383456', name: 'ICONCEPT', city: 'PAU', zip: '64000'},
        {id:'383437', name: 'ISWITCH', city: 'AMIENS', zip: '80000'},
        {id:'362643', name: 'EASY COMPUTER CENTRAL', city: 'NANCY', zip: '54000'},
        {id:'357935', name: 'INTER-ACTIF', city: 'TROYES', zip: '10000'},
        {id:'348916', name: 'SYMBIOSE INFORMATIQUE', city: 'ANGERS', zip: '49000'},
        {id:'334099', name: 'INFORMATIQUE ET PREVENTION', city: 'ANGOULEME', zip: '16000'},
        {id:'304554', name: 'FBX SYSTEME', city: 'CLERMONT-FERRAND', zip: '63000'},
        {id:'291637', name: 'ISWITCH', city: 'LENS', zip: '62300'},
        {id:'283362', name: 'EASY COMPUTER METZ', city: 'METZ', zip: '57000'},
        {id:'276470', name: 'LDK2', city: 'STRASBOURG', zip: '67000'},
        {id:'273853', name: 'MICRO COMPUTER SERVICES M C S', city: 'CANNES', zip: '06400'},
        {id:'266194', name: 'A.C.T.I. MAC', city: 'ROUEN', zip: '76000'},
        {id:'254746', name: '1FORMATIK PARTNERS', city: 'VERSAILLES', zip: '78000'},
        {id:'251970', name: 'ESPACES CONSEIL', city: 'ORLEANS', zip: '45000'},
        {id:'251939', name: 'GDA', city: 'MONTPELLIER', zip: '34000'},
        {id:'211408', name: 'OLYS', city: 'LYON', zip: '69003'},
        {id:'210323', name: 'EPHESUS', city: 'LYON', zip: '69006'},
        {id:'112176', name: 'ICONCEPT', city: 'TOULOUSE', zip: '31000'},
        {id:'112175', name: 'ICONCEPT', city: 'BORDEAUX', zip: '33000'}
    ];

    $scope.wave = {
        wave_name: 'iPhone 5s - France'
    }

    /**
     * Select a POS to display
     * @param pos
     */
    $scope.select = function(pos){
        $scope.current = pos;
        if(pos)
        {
            $location.search({pos: pos.id})
        }
        else
        {
            $location.search({})
        }
    }

    //Autoloading
    var id = $location.search().pos;
    if(id)
    {
        $scope.list.forEach(function(pos){
            if(pos.id == id)
            {
                $scope.select(pos);
            }
        });
    }
}