<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Tests\Files;

use bheller\ImagesGenerator\ImagesGeneratorProvider;
use Eulogix\Cool\Bundle\CoreBundle\Tests\Cases\baseTestCase;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\File\CoolTableFileRepository;
use Eulogix\Cool\Lib\File\FileProxyInterface;
use Eulogix\Cool\Lib\File\FileRepositoryPreviewProvider;
use Eulogix\Cool\Lib\File\SimpleFileProxy;
use Eulogix\Cool\Lib\Security\CoolUser;
use Faker\Factory;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FilesTest extends baseTestCase
{

    public function tdestHashing() {

    }

    public function testMemoryUsage()
    {

        $this->logMemoryUsage();
        ini_set('memory_limit', '10M');
        ini_set('display_errors', true);

        $user = CoolUser::fromId(1);

        /**
         * @var CoolTableFileRepository $frepo
         */
        
        $frepo = $user->getAccount()->getFileRepository();
        $this->populatePictures($frepo, 20);

        $previewProvider = new FileRepositoryPreviewProvider($frepo);
        $files = $frepo->getChildrenOf(null);

        foreach($files->getIterator() as $file) {
            /**
             * @var FileProxyInterface $file
             */
            if(!$file->isDirectory()) {
                $thumb = $previewProvider->getOrCreateCachedPreviewIcon($file->getId(), 80);
            }
        }

        //we made it here, so memory usage is fine
        $this->assertTrue(true);
    }

    /**
     * @param CoolTableFileRepository $frepo
     * @param int $mb
     */
    private function populatePictures(CoolTableFileRepository $frepo, $mb) {

        $frepo->wipeFolder(null, function(FileProxyInterface $file){
           // echo $file->getName()." deleted.\n";
        });

        $this->logMemoryUsage("populate start");

        $faker = Factory::create();
        $faker->addProvider(new ImagesGeneratorProvider($faker));
        $faker->seed(8923682);

        $folderSize = 0;
        $pictureCount = 0;
        do {
            $randomPicture = $faker->imageGenerator(Cool::getInstance()->getFactory()->getSettingsManager()->getTempFolder(), $faker->numberBetween(8000, 10240), $faker->numberBetween(6000, 8000), 'jpg', true, $faker->word, $faker->hexColor, $faker->hexColor);
            $folderSize += filesize($randomPicture);
            $proxy = SimpleFileProxy::fromFileSystem($randomPicture);

            $proxy->setName("picture{$pictureCount}.jpg");
            $frepo->storeFileAt($proxy);
            if($pictureCount%100==0) {
                $this->logMemoryUsage("{$pictureCount} pictures (folder size is ".readable_filesize($folderSize).") ");
            }
            @unlink($randomPicture);
            $pictureCount++;
        } while($folderSize < $mb*1024*1024);

        echo sprintf("\ncreated and stored %s (%s) fake pictures\n", $pictureCount, readable_filesize($folderSize));
    }

    private function logMemoryUsage($message = null) {
        echo sprintf("\n%s memory usage: %s \n", $message, readable_filesize(memory_get_usage()));
    }

}