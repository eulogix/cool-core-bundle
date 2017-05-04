<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core;

class Dictionary extends BaseDictionary {

    public function getSettings() {
        $pr = parent::getSettings();
        
        /* 
        
        1. entire hierarchy
        
        $myRuntimeOverrides = array(
             'atable'=> array (
                'attributes' => 
                    array (
                        'editable' => rand()?true:false,
                    ),
                'columns' => 
                    array (
                        'afieldname' => array (
                            'control_type' => rand()?'CBOX:AVALUE':'CBOX:ANOTHERVALUE',
                        ),
                    )
                )
        );
        
        return array_merge(\$pr,\$myRuntimeOverrides); 
         
        */
        
        /* 
        
        2. specific settings
        
        $pr['atable']['attributes']['editable'] = rand()?true:false;
        $pr['atable']['columns']['afieldname']['control']['_type'] = rand()?'CBOX:AVALUE':'CBOX:ANOTHERVALUE';
        
        */
        
        return $pr;
    }
    
}