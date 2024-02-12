<?php

namespace App\Tests\Model;

class TestMemberData
{
    protected string $xml;

    protected TestEpMember $model;

    public function __construct(protected readonly array $data)
    {
        $xml = new \SimpleXMLElement('<mep/>');
        $d = array_flip($data);
        array_walk_recursive($d, [$xml, 'addChild']);
        unset($d);
        $this->xml   = $xml->asXML();
        $this->model = new TestEpMember(...$this->data);
        $this->model->setNameParts(explode(" ", $this->model->getFullName(), 2));
    }

    public function getXml(): string
    {
        return $this->xml;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setModel(TestEpMember $model): void
    {
        $this->model = $model;
    }

    public function getModel(): TestEpMember
    {
        return $this->model;
    }

}
