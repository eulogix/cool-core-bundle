<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\CWidget\Core\Rule;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippet;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippetQuery;
use Eulogix\Cool\Lib\Form\CoolForm;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class RuleCodeEditorForm extends CoolForm {

    public function build() {

        parent::build();

        $this->getField('code_snippet_id')->setOnChange("widget.mixAction('refresh');");
        $this->setUpFields();

        return $this;
    }

    private function setUpFields() {
        if($snippet = $this->getSnippet()) {
            $this->getField('raw_code')->setValue('')->setReadOnly(true);
            $varValues = json_decode($this->getField('code_snippet_variables')->getValue(), true);
            $snippetVars = $snippet->getCodeSnippetVariables();
            foreach($snippetVars as $var) {
                $this->addFieldTextBox('snippet_var_'.$var->getName())->setValue(@$varValues[ $var->getName() ]);
            }
        } else $this->getField('raw_code')->setReadOnly(false);
    }

    public function onRefresh() {
        $parameters = $this->request->all();
        $this->rawFill( $parameters );
        $this->setUpFields();
    }

    public function onSubmit() {
        $this->onRefresh();

        $parameters = $this->request->all();
        $this->rawFill($parameters);

        $snippetVarsJson = [];

        if($snippet = $this->getSnippet()) {
            $snippetVars = $snippet->getCodeSnippetVariables();
            foreach($snippetVars as $var) {
                $snippetVarsJson[ $var->getName() ] = $this->getField('snippet_var_'.$var->getName())->getValue();
            }
        }

        $this->request->set('code_snippet_variables', json_encode($snippetVarsJson));

        parent::onSubmit();
    }

    /**
     * @inheritdoc
     */
    public function isConfigurable() {
        return false;
    }

    /**
     * @return CodeSnippet
     */
    public function getSnippet() {
        if($snippetId = $this->getField('code_snippet_id')->getValue())
            return CodeSnippetQuery::create()->findPk($snippetId);
    }

    /**
     * @inheritdoc
     */
    public function getLayout() {

        $snippetFields = "";
        if($snippet = $this->getSnippet()) {
            $snippetVars = $snippet->getCodeSnippetVariables();
            foreach($snippetVars as $var) {
                $snippetFields.="\n".'<raw>'.$var->getDescription().'</raw>,snippet_var_'.$var->getName().":400|nolabel";
            }
        }

        return "<FIELDS c1w='250px'>
type:200,name:400
raw_code:100%:200@!
code_snippet_id:200{$snippetFields}
save|align=center
</FIELDS>";
    }
}