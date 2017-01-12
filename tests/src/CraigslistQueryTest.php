<?php namespace JobApis\Jobs\Client\Test;

use JobApis\Jobs\Client\Queries\CraigslistQuery;
use Mockery as m;

class CraigslistQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CraigslistQuery
     */
    protected $query;

    public function setUp()
    {
        $this->query = new CraigslistQuery();
    }

    public function testItCanGetBaseUrlWhenLocationNotSet()
    {
        $this->assertEquals(
            'http://.craigslist.org/search/jjj',
            $this->query->getBaseUrl()
        );
    }

    public function testItCanGetBaseUrlWhenLocationSet()
    {
        $location = uniqid();
        $this->query->set('location', $location);
        $this->assertEquals(
            'http://'.$location.'.craigslist.org/search/jjj',
            $this->query->getBaseUrl()
        );
    }

    public function testItCanGetKeyword()
    {
        $keyword = uniqid();
        $this->query->set('query', $keyword);
        $this->assertEquals($keyword, $this->query->getKeyword());
    }

    public function testItReturnsFalseIfRequiredAttributesMissing()
    {
        $this->assertFalse($this->query->isValid());
    }

    public function testItReturnsTrueIfRequiredAttributesPresent()
    {
        $this->query->set('location', uniqid());

        $this->assertTrue($this->query->isValid());
    }

    public function testItSetsDefaultAttributes()
    {
        $this->assertEquals($this->query->get('format'), 'rss');
    }

    public function testItCanAddAttributesToUrl()
    {
        $this->query->set('query', uniqid());

        $url = $this->query->getUrl();

        $this->assertContains('query', $url);
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testItThrowsExceptionWhenSettingInvalidAttribute()
    {
        $this->query->set(uniqid(), uniqid());
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testItThrowsExceptionWhenGettingInvalidAttribute()
    {
        $this->query->get(uniqid());
    }

    public function testItSetsAndGetsValidAttributes()
    {
        $attributes = [
            'query' => uniqid(),
            'location' => uniqid(),
            's' => rand(1,100),
        ];

        foreach ($attributes as $key => $value) {
            $this->query->set($key, $value);
        }

        foreach ($attributes as $key => $value) {
            $this->assertEquals($value, $this->query->get($key));
        }
    }
}
