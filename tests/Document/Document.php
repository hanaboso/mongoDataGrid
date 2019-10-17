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
    private $string;

    /**
     * @var integer
     *
     * @ODM\Field(type="int")
     */
    private $int;

    /**
     * @var float
     *
     * @ODM\Field(type="float")
     */
    private $float;

    /**
     * @var bool
     *
     * @ODM\Field(type="bool")
     */
    private $bool;

    /**
     * @var DateTime
     *
     * @ODM\Field(type="date")
     */
    private $date;

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
