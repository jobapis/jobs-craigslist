<?php namespace JobApis\Jobs\Client\Providers\Test;

use JobApis\Jobs\Client\Collection;
use JobApis\Jobs\Client\Job;
use JobApis\Jobs\Client\Providers\CraigslistProvider;
use JobApis\Jobs\Client\Queries\CraigslistQuery;
use Mockery as m;

class CraigslistProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MonsterProvider
     */
    public $client;

    public function setUp()
    {
        $this->query = m::mock(CraigslistQuery::class);

        $this->client = new CraigslistProvider($this->query);
    }

    public function testItCanGetDefaultResponseFields()
    {
        $fields = [
            'title',
            'link',
            'description',
        ];
        $this->assertEquals($fields, $this->client->getDefaultResponseFields());
    }

    public function testItCanGetListingsPath()
    {
        $this->assertEquals('item', $this->client->getListingsPath());
    }

    public function testItCanGetFormat()
    {
        $this->assertEquals('xml', $this->client->getFormat());
    }

    public function testItCanCreateJobObjectWhenLocationNotSet()
    {
        $payload = $this->createJobArray();

        $this->query->shouldReceive('get')
            ->once()
            ->andReturn(null);

        $results = $this->client->createJobObject($payload);

        $this->assertInstanceOf(Job::class, $results);
        $this->assertEquals($payload['title'], $results->getTitle());
        $this->assertEquals($payload['title'], $results->getName());
        $this->assertEquals($payload['description'], $results->getDescription());
        $this->assertEquals($payload['link'], $results->getUrl());
    }

    public function testItCanCreateJobObjectWhenLocationSet()
    {
        $location = uniqid();
        $payload = $this->createJobArray();

        $this->query->shouldReceive('get')
            ->with('location')
            ->once()
            ->andReturn($location);

        $results = $this->client->createJobObject($payload);

        $this->assertInstanceOf(Job::class, $results);
        $this->assertEquals($payload['title'], $results->getTitle());
        $this->assertEquals($payload['title'], $results->getName());
        $this->assertEquals($payload['description'], $results->getDescription());
        $this->assertEquals($payload['link'], $results->getUrl());
        $this->assertEquals($location, $results->getLocation());
    }

    /**
     * Integration test for the client's getJobs() method.
     * @group real
     */
    public function testItCanGetJobs()
    {
        $options = [
            'query' => uniqid(),
            'location' => uniqid(),
            's' => rand(100,500),
        ];

        $guzzle = m::mock('GuzzleHttp\Client');

        $query = new CraigslistQuery($options);

        $client = new CraigslistProvider($query);

        $client->setClient($guzzle);

        $response = m::mock('GuzzleHttp\Message\Response');

        $jobs = $this->createXmlResponse();

        $guzzle->shouldReceive('get')
            ->with($query->getUrl(), [])
            ->once()
            ->andReturn($response);
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn($jobs);

        $results = $client->getJobs();

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(3, $results);
    }

    /**
     * Integration test with actual API call to the provider.
     */
    public function testItCanGetJobsFromApi()
    {
        if (!getenv('REAL_CALL')) {
            $this->markTestSkipped('REAL_CALL not set. Real API call will not be made.');
        }

        $keyword = 'sales';
        $location = 'chicago';

        $query = new CraigslistQuery([
            'query' => $keyword,
            'location' => $location,
        ]);

        $client = new CraigslistProvider($query);

        $results = $client->getJobs();

        $this->assertInstanceOf('JobApis\Jobs\Client\Collection', $results);

        foreach($results as $job) {
            $this->assertEquals($keyword, $job->query);
            $this->assertEquals($location, $job->location);
        }
    }

    private function createJobArray()
    {
        return [
            'title' => uniqid(),
            'link' => uniqid(),
            'description' => uniqid(),
        ];
    }

    private function createXmlResponse()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<rdf:RDF
 xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
 xmlns="http://purl.org/rss/1.0/"
 xmlns:enc="http://purl.oclc.org/net/rss_2.0/enc#"
 xmlns:ev="http://purl.org/rss/1.0/modules/event/"
 xmlns:content="http://purl.org/rss/1.0/modules/content/"
 xmlns:dcterms="http://purl.org/dc/terms/"
 xmlns:syn="http://purl.org/rss/1.0/modules/syndication/"
 xmlns:dc="http://purl.org/dc/elements/1.1/"
 xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/"
 xmlns:admin="http://webns.net/mvcb/"
>

<channel rdf:about="https://chicago.craigslist.org/search/jjj?format=rss&#x26;s=100&#x26;searchNearby=1">
<title>craigslist chicago | jobs search </title>
<link>https://chicago.craigslist.org/search/jjj?s=100&#x26;searchNearby=1</link>
<description></description>
<dc:language>en-us</dc:language>
<dc:rights>copyright 2017 craiglist</dc:rights>
<dc:publisher>robot@craigslist.org</dc:publisher>
<dc:creator>robot@craigslist.org</dc:creator>
<dc:source>https://chicago.craigslist.org/search/jjj?format=rss&#x26;s=100&#x26;searchNearby=1</dc:source>
<dc:title>craigslist chicago | jobs search </dc:title>
<dc:type>Collection</dc:type>
<syn:updateBase>2017-01-12T14:57:17-06:00</syn:updateBase>
<syn:updateFrequency>6</syn:updateFrequency>
<syn:updatePeriod>hourly</syn:updatePeriod>
<items>
 <rdf:Seq>
  <rdf:li rdf:resource="http://chicago.craigslist.org/chc/mar/5956388074.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/chc/trp/5956386485.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/wcl/trd/5956386068.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/chc/trp/5956385679.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/nch/csr/5956383405.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/nwc/ofc/5956382892.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/sox/trp/5956380424.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/wcl/mnu/5956380417.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/chc/trd/5956379177.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/nwi/lab/5956379141.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/nwc/lab/5956378075.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/nwc/trd/5956377727.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/nch/lab/5956377057.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/sox/trp/5956375812.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/sox/ofc/5956374179.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/nwc/lab/5956373540.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/chc/trp/5956371665.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/sox/etc/5956362640.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/chc/csr/5956361854.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/chc/lab/5956361855.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/chc/ofc/5956361819.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/nwc/lab/5956361365.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/chc/fbh/5956355279.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/chc/trp/5956354030.html" />
  <rdf:li rdf:resource="http://chicago.craigslist.org/wcl/acc/5956353636.html" />
 </rdf:Seq>
</items>
</channel>
<item rdf:about="http://chicago.craigslist.org/chc/mar/5956388074.html">
<title><![CDATA[Lyft Brand Marketing Representative]]></title>
<link>http://chicago.craigslist.org/chc/mar/5956388074.html</link>
<description><![CDATA[Ambassadors are key players at Lyft. You\'re in charge of growing Lyft in your city, with the flexibility of promoting on your own schedule. When you do spend time referring, we\'ll thank you in cash. 
Why Become a Lyft Ambassador?
� Cash for every new [...]]]></description>
<dc:date>2017-01-12T12:37:36-06:00</dc:date>
<dc:language>en-us</dc:language>
<dc:rights>copyright 2017 craiglist</dc:rights>
<dc:source>http://chicago.craigslist.org/chc/mar/5956388074.html</dc:source>
<dc:title><![CDATA[Lyft Brand Marketing Representative]]></dc:title>
<dc:type>text</dc:type>
<dcterms:issued>2017-01-12T12:37:36-06:00</dcterms:issued>
</item>
<item rdf:about="http://chicago.craigslist.org/chc/trp/5956386485.html">
<title><![CDATA[Local CDL Driver. Class A, Cicero, IL]]></title>
<link>http://chicago.craigslist.org/chc/trp/5956386485.html</link>
<description><![CDATA[We have a local driver position open. 
- At least 2 years of experience and clean record 
- 1099 tax form 
- Weekends OFF 
- 150 miles radius from Cicero 
- Volvo trucks 
- Multilingual Dispatchers 
- Weekly paycheck 
Please call 
 <a href="/fb/chi/trp/5956386485" class="showcontact" title="click to show contact info">show contact info</a>
]]></description>
<dc:date>2017-01-12T12:36:41-06:00</dc:date>
<dc:language>en-us</dc:language>
<dc:rights>copyright 2017 craiglist</dc:rights>
<dc:source>http://chicago.craigslist.org/chc/trp/5956386485.html</dc:source>
<dc:title><![CDATA[Local CDL Driver. Class A, Cicero, IL]]></dc:title>
<dc:type>text</dc:type>
<dcterms:issued>2017-01-12T12:36:41-06:00</dcterms:issued>
</item>
<item rdf:about="http://chicago.craigslist.org/wcl/trd/5956386068.html">
<title><![CDATA[Appliance Repair Technician (Kaneville)]]></title>
<link>http://chicago.craigslist.org/wcl/trd/5956386068.html</link>
<description><![CDATA[We are an Appliance Repair Company located in Kaneville, IL. that services the Fox Valley area. 
Responsibilities: 
Appliance Repair Technician is responsible for the diagnosis and repair of appliances in customer homes. Products repaired include the [...]]]></description>
<dc:date>2017-01-12T12:36:26-06:00</dc:date>
<dc:language>en-us</dc:language>
<dc:rights>copyright 2017 craiglist</dc:rights>
<dc:source>http://chicago.craigslist.org/wcl/trd/5956386068.html</dc:source>
<dc:title><![CDATA[Appliance Repair Technician (Kaneville)]]></dc:title>
<dc:type>text</dc:type>
<dcterms:issued>2017-01-12T12:36:26-06:00</dcterms:issued>
</item>
</rdf:RDF>';
    }
}
