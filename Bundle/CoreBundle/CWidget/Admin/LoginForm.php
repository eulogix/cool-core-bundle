<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\CWidget\Admin;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Security\CoolUser;
use Eulogix\Cool\Lib\Traits\ContainerHolder;
use Eulogix\Cool\Lib\Form\Form;
use Eulogix\Cool\Lib\Widget\Message;
use Eulogix\Lib\Validation\ConstraintBuilder as C;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginForm extends Form {
    
    use ContainerHolder;

    public function build() {
        parent::build();
        
        $this->addFieldTextBox("user")
            ->setConstraints(C::NotBlank_())
            ->setValue('admin');
        
        $this->addFieldTextBox("password")
            ->setConstraints(C::NotBlank_())
            ->setIsPassword(true)
            ->setValue('');
        
        $this->addFieldSubmit("login");

        return $this;
   }  
   
   public function onSubmit() {     
       
       $parameters = $this->request->all();

       $this->fill( $parameters );

        if( $this->validate( array_keys($parameters) ) ) {
           
            if($user = Cool::getInstance()->getFactory()->getUserManager()->getUserByLoginName($parameters['user'])) {
                if($user->getHashedPassword() == md5($parameters['password'])) {

                    $securityContext = $this->getContainer()->get('security.token_storage');
                    // Here, "secured_area" is the name of the firewall in your security.yml
                    $token = new UsernamePasswordToken($nu = new CoolUser( $user ), $parameters['password'], 'secured_area', $nu->getRoles() );
                    $securityContext->setToken($token);

                    // Fire the login event
                    $event = new InteractiveLoginEvent( $this->getContainer()->get('request'), $token);
                    $this->getContainer()->get("event_dispatcher")->dispatch("security.interactive_login", $event);

                    if($user->getDefaultLocale())
                        Cool::getInstance()->getFactory()->getSession()->setLocale( $user->getDefaultLocale() );

                    $url = $this->getContainer()->get('router')->generate('_coolAdminDesktop');
                    $this->addCommandJs("document.location = '$url';");
                        
                } else {
                    $this->addMessage(Message::TYPE_ERROR, "PASSWORD NOT VALID");    
                }   
            } else {
                $this->addMessage(Message::TYPE_ERROR, "USER DOES NOT EXIST");    
            }
        } else {
            $this->addMessage(Message::TYPE_ERROR, "NOT VALIDATED");
        }
  } 
   
  public function getDefaultLayout() {
      $pl = parent::getDefaultLayout();
      return 
'<table style="width:100%" margin=0>
<tr>
    <td valign=top style="width:180px; text-align:center"><img src="/bundles/eulogixcoolcore/gfx/admin/login_logo.png"></td>
    <td valign=top><b>{{"TITLE"|t}}<b><br><br>
    '.$pl.'</td>
</table>';
  }

}