<?php declare(strict_types=1);

namespace MongoDataGridTests\Integration;

use Exception;
use Hanaboso\MongoDataGrid\GridHandlerTrait;
use Hanaboso\MongoDataGrid\GridRequestDto;
use MongoDataGridTests\TestCaseAbstract;

/**
 * Class GridHandlerTraitTest
 *
 * @package MongoDataGridTests\Integration
 */
final class GridHandlerTraitTest extends TestCaseAbstract
{

    use GridHandlerTrait;

    protected const DATABASE = 'datagrid-trait';

    /**
     * @throws Exception
     */
    public function testGetGridResponse(): void
    {
        $dto = new GridRequestDto([]);
        $res = $this->getGridResponse($dto, ['a']);

        self::assertEquals(
            [
                'items'  => ['a'],
                'filter' => [],
                'sorter' => [],
                'paging' => [
                    'page'         => 1,
                    'itemsPerPage' => 10,
                    'total'        => 0,
                    'nextPage'     => 1,
                    'lastPage'     => 1,
                    'previousPage' => 1,
                ],
            ],
            $res,
        );
    }

}
