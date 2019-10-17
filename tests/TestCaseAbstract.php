<?php declare(strict_types=1);

namespace MongoDataGridTests;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Exception;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\PrivateTrait;
use MongoDataGridTests\Document\Document;
use PHPUnit\Framework\TestCase;

/**
 * Class TestCaseAbstract
 *
 * @package MongoDataGridTests
 */
abstract class TestCaseAbstract extends TestCase
{

    use PrivateTrait;

    private const TEMP_DIR = '%s/../temp/Doctrine2.ODM';
    private const HOSTNAME = 'mongo';
    private const DATABASE = 'datagrid';

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        AnnotationRegistry::registerLoader('class_exists');

        $configuration = new Configuration();
        $configuration->setProxyNamespace('Proxy');
        $configuration->setHydratorNamespace('Hydrator');
        $configuration->setProxyDir(sprintf(self::TEMP_DIR, __DIR__));
        $configuration->setHydratorDir(sprintf(self::TEMP_DIR, __DIR__));
        $configuration->setMetadataDriverImpl(AnnotationDriver::create([sprintf('%s/Document', __DIR__)]));
        $configuration->setDefaultDB(self::DATABASE);

        $this->dm = DocumentManager::create(new Connection(self::HOSTNAME), $configuration);
        $this->dm->getConnection()->dropDatabase(self::DATABASE);
        $this->dm->getSchemaManager()->createCollections();
        $this->dm->getSchemaManager()->ensureDocumentIndexes(Document::class);
    }

}
