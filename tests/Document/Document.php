<?php declare(strict_types=1);

namespace MongoDataGridTests\Document;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class Document
 *
 * @package MongoDataGridTests\Document
 *
 * @ODM\Document
 * @ODM\Indexes({
 *     @ODM\Index(keys={"string"="text", "int"="text", "float"="text"})
 * })
 */
final class Document
{

    /**
     * @var string
     *
     * @ODM\Id()
     */
    private $id;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    private string $string;

    /**
     * @var integer
     *
     * @ODM\Field(type="int")
     */
    private int $int;

    /**
     * @var float
     *
     * @ODM\Field(type="float")
     */
    private float $float;

    /**
     * @var bool
     *
     * @ODM\Field(type="bool")
     */
    private bool $bool;

    /**
     * @var DateTime
     *
     * @ODM\Field(type="date")
     */
    private DateTime $date;

    /**
     * Document constructor.
     */
    public function __construct()
    {
        $this->int    = 0;
        $this->float  = 0.0;
        $this->bool   = TRUE;
        $this->string = '';
        $this->date   = new DateTime();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getString(): string
    {
        return $this->string;
    }

    /**
     * @param string $string
     *
     * @return Document
     */
    public function setString(string $string): Document
    {
        $this->string = $string;

        return $this;
    }

    /**
     * @return int
     */
    public function getInt(): int
    {
        return $this->int;
    }

    /**
     * @param int $int
     *
     * @return Document
     */
    public function setInt(int $int): Document
    {
        $this->int = $int;

        return $this;
    }

    /**
     * @return float
     */
    public function getFloat(): float
    {
        return $this->float;
    }

    /**
     * @param float $float
     *
     * @return Document
     */
    public function setFloat(float $float): Document
    {
        $this->float = $float;

        return $this;
    }

    /**
     * @return bool
     */
    public function isBool(): bool
    {
        return $this->bool;
    }

    /**
     * @param bool $bool
     *
     * @return Document
     */
    public function setBool(bool $bool): Document
    {
        $this->bool = $bool;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     *
     * @return Document
     */
    public function setDate(DateTime $date): Document
    {
        $this->date = $date;

        return $this;
    }

}
