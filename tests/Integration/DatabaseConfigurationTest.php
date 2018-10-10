<?php declare(strict_types=1);

namespace Tests\Integration;

use Tests\Document\Document;
use Tests\TestCaseAbstract;

/**
 * Class DatabaseConfigurationTest
 *
 * @package Tests\Integration
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