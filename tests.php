<?

include_once "template.php";
include "vendor/autoload.php";

class TemplateTest extends PHPUnit_Framework_TestCase {
    
    function testSub() {
        $ret = substitute_values(
            "Hello {FIRST_NAME}",
            array('FIRST_NAME' => "Eric"), 
            false);
        $this->assertEquals($ret, "Hello Eric");
    }

    function testEntities() {
        $ret = substitute_values(
            "Hello {VALUE}",
            array('VALUE' => 'Bert&Ernie'),
            true);
        $this->assertEquals($ret, "Hello Bert&amp;Ernie");
    }

    function testMissing() {
        $this->setExpectedException('MissingKeyException');
        $ret = substitute_values(
            "Hello {VALUE}",
            array('NOPE' => 'Bert&Ernie'),
            true);
    }


}
