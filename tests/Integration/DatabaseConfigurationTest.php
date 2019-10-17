<?php declare(strict_types=1);

namespace MongoDataGridTests\Integration;

use MongoDataGridTests\Document\Document;
use MongoDataGridTests\TestCaseAbstract;

/**
 * Class DatabaseConfigurationTest
 *
 * @package MongoDataGridTests\Integration
 */
final class DatabaseConfigurationTest extends TestCaseAbstract
{

    /**
     *
     */
    public function testConnection(): void
    {
        $this->dm->persist((new Document())->setString('Document'));
        $this->dm->flush();
        $this->dm->clear();

        /** @var Document[] $documents */
        $documents = $this->dm->getRepository(Document::class)->findAll();
        self::assertEquals(1, count($documents));
        self::assertEquals('Document', $documents[0]->getString());
    }

}
