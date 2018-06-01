<?php
/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\CWidget\Files\Editor;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\File\FileRepositoryFactory;
use Eulogix\Cool\Lib\File\FileRepositoryInterface;
use Eulogix\Cool\Lib\File\FileSystemFileRepository;
use Eulogix\Cool\Lib\File\FileUtil;
use Eulogix\Cool\Lib\Form\Form;
use Eulogix\Cool\Lib\Template\TwigTemplate;
use Eulogix\Cool\Lib\Widget\Message;
use Eulogix\Lib\File\Proxy\FileProxyInterface;
use Eulogix\Lib\File\Proxy\SimpleFileProxy;
use Eulogix\Lib\File\ZipUtils;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class TwigTemplateEditorForm extends Form
{

    /**
     * @var FileRepositoryInterface
     */
    protected $fileRepository;

    public static function getClientWidget() {
        return 'cool/file/twigTemplateEditor';
    }

    /**
     * @inheritdoc
     */
    public function build() {
        parent::build();

        $template = $this->getTemplateProxy();
        if(preg_match('/(htm*|zip)/sim', $template->getExtension())) {
            $this->addFieldHTMLEditor("template")->useCKEditor();
        } else $this->addFieldTextArea("template");

        $this->addFieldHidden('tempRepoKey');
        $this->addFieldHidden('innerTemplateName');
        $this->addFieldHidden('zippedTemplate');
        $this->addFieldHidden('sampleData');

        $this->initTempRepo();

        $this->addFieldSubmit('save');

        $this->addCommandJs("
            if(widget.dialog) {
                require(['dojo/dom-geometry'], function(domGeometry) {
                    var contentBox = domGeometry.getContentBox(widget.contentNode);
                        if(widget.getField('template').editor)
                            widget.getField('template').editor.on('loaded', function(evt){
                               widget.getField('template').editor.resize('100%', contentBox.h - 100);
                            });
                });
            }
        ");

        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function initTempRepo() {
        $templateField = $this->getField('template');

        if($this->getRequest()->has('tempRepoKey')){
            $key = $this->getRequest()->get('tempRepoKey');
        } else {
            $tempFolder = FileUtil::getTempFolder();

            $template = $this->getTemplateProxy();
            if($template->getExtension() == 'zip') {
                ZipUtils::unpack($template, $tempFolder);
                $innerTemplateName = 'template.html.twig';
                $this->getField('zippedTemplate')->setValue(1);
            } else {
                $innerTemplateName = $template->getName();
                $template->toFile($tempFolder.DIRECTORY_SEPARATOR.$innerTemplateName);
            }

            $tempRepo = new FileSystemFileRepository($tempFolder);
            $key = FileRepositoryFactory::register($tempRepo);

            $this->fixRelativePaths($tempFolder, $key);

            $templateContent = file_get_contents($tempFolder.DIRECTORY_SEPARATOR.$innerTemplateName);
            $isUTF8 = preg_match('//u', $templateContent);
            $validUTF8 = mb_check_encoding($templateContent, 'UTF-8');

            $enc = mb_detect_encoding($templateContent, ['UTF-8', 'ASCII',
                'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5',
                'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10',
                'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16',
                'Windows-1251', 'Windows-1252', 'Windows-1254']);

            if(!$isUTF8 || !$validUTF8) {
                $this->addMessageWarning("Template encoding appears to be $enc. It has been converted to UTF-8");
                $templateContent = iconv($enc, 'UTF-8', $templateContent);
            }

            $templateField->setValue($templateContent);
            $this->getField('tempRepoKey')->setValue($key);
            $this->getField('innerTemplateName')->setValue($innerTemplateName);


        }
        if(method_exists($templateField, 'setUploadRepoId'))
            $templateField->setUploadRepoId($key, '/ck_uploads');
    }

    /**
     * @return FileProxyInterface
     */
    public function getTemplateProxy() {
        return $this->getFileRepository()->get( $this->getParameters()->get('filePath') );
    }

    /**
     * @return FileRepositoryInterface
     * @throws \Exception
     */
    public function getFileRepository() {
        if($this->fileRepository) return $this->fileRepository;
        if($repositoryArgs = json_decode($this->getParameters()->get('repositoryParameters'), true)) {
            $repoId = $repositoryArgs['repositoryId'];
            $this->fileRepository = FileRepositoryFactory::fromId( $repoId );
            $this->fileRepository->setParameters( $repositoryArgs );
        } else throw new \Exception("check parameters");
        return $this->fileRepository;
    }

    /**
     * @return FileSystemFileRepository
     * @throws \Exception
     */
    public function getTempFileRepository() {
        return FileRepositoryFactory::fromId( $this->request->get('tempRepoKey') );
    }

    /**
     * @inheritdoc
     */
    public function onSubmit() {
        $request = $this->request->all();
        $this->rawFill( $request );

        if($this->validate( array_keys($request) ) ) {
            try {

                $innerTemplateName = $this->request->get('innerTemplateName');
                $tempFolder = $this->getTempFileRepository()->getBaseFolder();
                file_put_contents($tempFolder.DIRECTORY_SEPARATOR.$innerTemplateName, $this->getField('template')->getValue());
                $this->cleanTemplate($tempFolder, $this->getBaseTempRepoUrl( $this->request->get('tempRepoKey') ));

                $oldTemplate = $this->getTemplateProxy();
                if($this->request->get('zippedTemplate')) {
                    $newTemplate = ZipUtils::zipFolder($tempFolder);
                } else {
                    $newTemplate = new SimpleFileProxy();
                    $newTemplate->setContent(file_get_contents($tempFolder.DIRECTORY_SEPARATOR.$innerTemplateName));
                }

                $newTemplate->setName($oldTemplate->getName());
                $newTemplate->setProperties($oldTemplate->getProperties());

                $this->getFileRepository()->storeFileAt($newTemplate, $oldTemplate->getParentId());

                $this->addMessageInfo("Template saved successfully");

            } catch(\Exception $e) {
                $this->addMessage(Message::TYPE_ERROR, $e->getMessage());
            }
        } else {
            $this->addMessage(Message::TYPE_ERROR, "NOT VALIDATED");
        }
    }

    /**
     * called when the dialog closes
     * @inheritdoc
     */
    public function onCleanup() {
        if( $baseFolder = $this->getTempFileRepository()->getBaseFolder() ) {
            if(strpos($baseFolder, realpath(Cool::getInstance()->getFactory()->getSettingsManager()->getTempFolder())) === 0)
                rrmdir($baseFolder);
            FileRepositoryFactory::unregister($this->request->get('tempRepoKey'));
        }
    }

    /**
     * @return mixed|string
     */
    public function onGetPreview() {

        $twigTemplate = new TwigTemplate();
        $template = new SimpleFileProxy();
        $template->setContent($this->getRequest()->get('template'));
        try {
            $twigTemplate->setTemplateFile($template);
            $twigTemplate->setData(json_decode($this->getRequest()->get('sampleData'), true));
            return ['preview' => $twigTemplate->getRenderedOutput()->getContent()];
        } catch (\Exception $e) {
            return ['preview' => $e->getMessage()];
        }
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return "COOL_TWIG_TEMPLATE_EDITOR";
    }

    public function getLayout() {
        return "<div style='width:100%; height: 100%;'><FIELDS>template:100%:500
save</FIELDS></div>";
    }

    /**
     * @param $tempFolder
     * @param $key
     * @return string
     */
    private function fixRelativePaths($tempFolder, $key)
    {
        $baseServeUrl = $this->getBaseTempRepoUrl($key);

        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tempFolder));

        /**
         * @var \SplFileInfo $file
         */
        foreach ($rii as $file)
            if (!$file->isDir()) {
                if($file->getExtension() == 'twig') {
                    $newContent = file_get_contents($file->getRealPath());
                    $newContent = preg_replace('%(?<=src=[\'"])(?!/)(.+?)(?=[\'"])%sim', "$baseServeUrl\$1", $newContent);
                    file_put_contents($file->getRealPath(), $newContent);
                }
            }
    }

    private function cleanTemplate($tempFolder, $baseServeUrl) {
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tempFolder));

        /**
         * @var \SplFileInfo $file
         */
        foreach ($rii as $file)
            if (!$file->isDir()) {
                if($file->getExtension() == 'twig') {
                    $newContent = file_get_contents($file->getRealPath());
                    $newContent = str_replace($baseServeUrl, '', $newContent);
                    file_put_contents($file->getRealPath(), $newContent);
                }
            }
    }

    private function getBaseTempRepoUrl($key) {
        return Cool::getInstance()->getContainer()->get('router')->generate('frepoServe', ['repositoryId' => $key, 'filePath' => '/']);
    }

}