<?php
namespace GbiliRe3Module;
return array(
    'factories' => array(
        'resolveScript' => function ($vhm) {
            $scriptalicious = $vhm->getServiceLocator()->get('Scriptalicious');
            $vh = new View\Helper\Re3($scriptalicious);
            return $vh;
        },
    ),
);
