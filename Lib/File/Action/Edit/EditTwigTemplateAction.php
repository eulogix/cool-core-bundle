<?php
/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\File\Action\Edit;

use Eulogix\Cool\Lib\File\Action\FileAction;
use Eulogix\Cool\Lib\Widget\Menu;
use Eulogix\Lib\File\Proxy\FileProxyInterface;
use Eulogix\Lib\File\ZipUtils;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class EditTwigTemplateAction extends FileAction
{

    /**
     * @inheritdoc
     */
    public function appliesTo(FileProxyInterface $file)
    {
        if($this->getFileRepository()->getUserPermissions()->canOverwrite($file->getId())) {
            $ext = $file->getCompleteExtension();
            if(preg_match('/twig/sim', $ext)) {
                return true;
            } else if($file->getCompleteExtension() == 'zip') {
                return in_array('template.html.twig', ZipUtils::getContentList($file));
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function populateContextualMenu(Menu $menu)
    {
        $item = $menu->addChildren();
        $item->setLabel("Edit template")
            ->setOnClick("
                         var d = COOL.getDialogManager().openWidgetDialog(
                            'EulogixCoolCoreBundle/Files/Editor/TwigTemplateEditorForm',
                            'Edit twig template',
                            {filePath: filePath, repositoryParameters: JSON.stringify(repository.getAllArgs()), hideCloseButton:true},
                            function(){ d.widget.mixAction('cleanup'); }
                         );
                     ");
    }
}