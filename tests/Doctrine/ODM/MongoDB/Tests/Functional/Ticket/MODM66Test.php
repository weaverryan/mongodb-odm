<?php

namespace Doctrine\ODM\MongoDB\Tests\Functional\Ticket;

require_once __DIR__ . '/../../../../../../TestInit.php';

use Doctrine\Common\Collections\ArrayCollection;

class MODM66Test extends \Doctrine\ODM\MongoDB\Tests\BaseTest
{

    public function testTest()
    {
        $b1 = new B('first');
        $a = new A(array($b1));
        $this->dm->persist($a);
        $this->dm->flush();
        $b2 = new B('second');
        $a->getB()->add($b2);
        $this->dm->flush();

        $this->dm->refresh($a);
        $b = $a->getB()->toArray();

        $this->assertEquals(2, count($b));

        $this->assertEquals(array(
            $b1->getId(), $b2->getId()
            ), array(
            $b[0]->getId(), $b[1]->getId()
        ));
    }

    public function testRefresh()
    {
        $b1 = new B('first');
        $a = new A(array($b1));
        $this->dm->persist($a);
        $this->dm->flush();
        $b2 = new B('second');

        $this->dm->refresh($a);

        $a->getB()->add($b2);
        $this->dm->flush();
        $this->dm->refresh($a);
        $b = $a->getB()->toArray();

        $this->assertEquals(2, count($b));

        $this->assertEquals(array(
            $b1->getId(), $b2->getId()
            ), array(
            $b[0]->getId(), $b[1]->getId()
        ));
    }

}

/** @Document(db="tests", collection="tests") */
class A
{

    /** @Id */
    protected $id;
    /** @ReferenceMany(targetDocument="b", cascade="all") */
    protected $b;

    function __construct($b)
    {
        $this->b = new ArrayCollection($b);
    }

    function getB()
    {
        return $this->b;
    }

}

/** @Document(db="tests", collection="tests2") */
class B
{

    /** @Id */
    protected $id;
    /** @String */
    protected $tmp;

    function __construct($v)
    {
        $this->tmp = $v;
    }

    public function getId()
    {
        return $this->id;
    }

}